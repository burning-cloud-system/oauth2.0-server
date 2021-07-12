<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Grant;

use BurningCloudSystem\OAuth2\Server\Crypt\CryptKey;
use BurningCloudSystem\OAuth2\Server\Crypt\CryptTrait;
use BurningCloudSystem\OAuth2\Server\Entities\AccessTokenEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\AuthorizationCodeEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\ClientEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\ScopeEntityInterface;
use BurningCloudSystem\OAuth2\Server\Event\Listener\ClientAuthenticationFailedInterface;
use BurningCloudSystem\OAuth2\Server\Event\RequestEvent;
use BurningCloudSystem\OAuth2\Server\Models\AuthorizationCodeModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ScopeModelInterface;
use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;
use BurningCloudSystem\OAuth2\Server\Exception\UniqueAuthorizationCodeIdentifierException;
use BurningCloudSystem\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use BurningCloudSystem\OAuth2\Server\Models\AccessTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\RefreshTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\UserModelInterface;
use BurningCloudSystem\OAuth2\Server\Request\AuthorizationRequest;
use BurningCloudSystem\OAuth2\Server\Request\Parame\GrantTypeParame;
use BurningCloudSystem\OAuth2\Server\Request\Parame\ResponseTypeParame;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use DateInterval;
use DateTimeImmutable;
use Error;
use Exception;
use League\Event\EventDispatcherAwareBehavior;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use TypeError;

abstract class AbstractGrant implements GrantInterface
{
    use EventDispatcherAwareBehavior, CryptTrait;

    const MAX_RANDOM_TOKEN_GENERATION_ATTEMPTS = 10;

    /**
     * @var ResponseTypeParame|null
     */
    protected ?ResponseTypeParame $responseTypeParame = null;

    /**
     * @var GrantTypeParame|null
     */
    protected ?GrantTypeParame $grantTypeParame = null;

    /**
     * @var CryptKey
     */
    protected CryptKey $privateKey;

    /**
     * @var DateInterval
     */
    protected DateInterval $accessTokenTTL;

    /**
     * @var DateInterval
     */
    protected DateInterval $refreshTokenTTL;

    /**
     * @var ClientModelInterface
     */
    protected ClientModelInterface $clientModel;

    /**
     * @var ScopeModelInterface
     */
    protected ScopeModelInterface $scopeModel;

    /**
     * @var AuthorizationCodeModelInterface
     */
    protected AuthorizationCodeModelInterface $authorizationCodeModel;

    /**
     * @var UserModelInterface
     */
    protected UserModelInterface $userModel;

    /**
     * @var AccessTokenModelInterface
     */
    protected AccessTokenModelInterface $accessTokenModel;

    /**
     * @var RefreshTokenModelInterface
     */
    protected RefreshTokenModelInterface $refreshTokenModel;

    /**
     * Default scope.
     * 
     * @var string|null
     */
    protected ?string $defaultScope = null;

    /**
     * construct.
     *
     * @param ClientModelInterface $clientModel
     * @param ScopeModelInterface $scopeModel
     */
    public function __construct(ClientModelInterface $clientModel,
                                ScopeModelInterface $scopeModel)
    {
        $this->setClientModel($clientModel);
        $this->setScopeModel($scopeModel);
    }

    /**
     * Set the private key.
     *
     * @param string|CryptKey $privateKey
     * @return void
     */
    public function setPrivateKey($privateKey) : void
    {
        if ($privateKey instanceof CryptKey === false)
        {
            $privateKey = new CryptKey($privateKey);
        }
        $this->privateKey = $privateKey;
    }

    /**
     * Get response type parame.
     *
     * @return ResponseTypeParame
     */
    protected function getResponseTypeParame() : ResponseTypeParame
    {
        return $this->responseTypeParame;
    }

    /**
     * Get grant type parame.
     *
     * @return GrantTypeParame
     */
    protected function getGrantTypeParame() : GrantTypeParame
    {
        return $this->grantTypeParame;
    }

    /**
     * {@inheritDoc}
     *
     * @param DateInterval $accessTokenTTL
     * @return void
     */
    public function setAccessTokenTTL(DateInterval $accessTokenTTL): void
    {
        $this->accessTokenTTL = $accessTokenTTL;
    }

    /**
     * {@inheritDoc}
     *
     * @param DateInterval $refreshTokenTTL
     * @return void
     */
    public function setRefreshTokenTTL(DateInterval $refreshTokenTTL): void
    {
        $this->refreshTokenTTL = $refreshTokenTTL;   
    }

    /**
     * {@inheritDoc}
     *
     * @param ClientModelInterface $clientModel
     * @return void
     */
    public function setClientModel(ClientModelInterface $clientModel) : void
    {
        $this->clientModel = $clientModel;
    }

    /**
     * {@inheritDoc}
     * 
     * @param ScopeModelInterface $scopeModel
     * @return void
     */
    public function setScopeModel(ScopeModelInterface $scopeModel) : void
    {
        $this->scopeModel = $scopeModel;
    }

    /**
     * {@inheritDoc}
     *
     * @param AccessTokenModelInterface $accessTokenModel
     * @return void
     */
    public function setAccessTokenModel(AccessTokenModelInterface $accessTokenModel): void
    {
        $this->accessTokenModel = $accessTokenModel;
    }

    /**
     * Set authorize code model.
     *
     * @param AuthorizationCodeModelInterface $authorizationCodeModel
     * @return void
     */
    public function setAuthorizationCodeModel(AuthorizationCodeModelInterface $authorizationCodeModel) : void
    {
        $this->authorizationCodeModel = $authorizationCodeModel;
    }

    /**
     * Set user model.
     *
     * @param UserModelInterface $userModel
     * @return void
     */
    public function setUserModel(UserModelInterface $userModel) : void
    {
        $this->userModel = $userModel;
    }

    /**
     * Set refresh token model.
     *
     * @param RefreshTokenModelInterface $refreshTokenModel
     * @return void
     */
    public function setRefreshTokenModel(RefreshTokenModelInterface $refreshTokenModel): void
    {
        $this->refreshTokenModel = $refreshTokenModel;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $defaultScope
     * @return void
     */
    public function setDefaultScope(string $defaultScope): void
    {
        $this->defaultScope = $defaultScope;
    }

    /**
     * {@inheritDoc}
     */
    public function canRespondToAuthorizationRequest(ServerRequestInterface $request) : bool
    {
        if (is_null($this->responseTypeParame)) 
        {
            $this->setResponseTypeParame($request, $this->getResponseTypeParameClassName());
        }
        $params = (array) $request->getQueryParams();
        return (array_key_exists(ResponseTypeParame::RESPONSE_TYPE, $params)
                && $this->responseTypeParame->responseType === $this->getResponseType());
    }

    /**
     * Validate scopes in the request.
     *
     * @param array $scopes
     * @param string|null $redirectUri
     * 
     * @throws OAuthException
     * 
     * @return ScopeEntityInterface[]
     */
    protected function validateScopes(array $scopes, ?string $redirectUri = null) : array
    {
        $validScopes = [];

        foreach($scopes as $scope) 
        {
            $scopeEntity = $this->scopeModel->getScopeEntity($scope);

            if ($scopeEntity instanceof ScopeEntityInterface === false)
            {
                throw OAuthException::invalidScope($scope, $redirectUri);
            }
            $validScopes[] = $scopeEntity;
        }

        return $validScopes;
    }

    /**
     * Set response type parame.
     *
     * @param ServerRequestInterface $request
     * @param string $className
     * @return void
     */
    protected function setResponseTypeParame(ServerRequestInterface $request, string $className) : void
    {
        $this->responseTypeParame = new $className;
        if ($this->responseTypeParame instanceof ResponseTypeParame)
        {
            $this->responseTypeParame->bindParame($request);
        }
        else 
        {
            throw new LogicException('This request is not an ResponseTypeParame.');
        }
    }

    /**
     * Set grant type parame.
     *
     * @param ServerRequestInterface $request
     * @param string $className
     * @return void
     */
    protected function setGrantTypeParame(ServerRequestInterface $request, string $className) : void
    {
        $this->grantTypeParame = new $className;
        if ($this->grantTypeParame instanceof GrantTypeParame)
        {
            $this->grantTypeParame->bindParame($request);
        }
        else
        {
            throw new LogicException('This request is not an GrantTypeParam.');
        }
    }

    /**
     * Issue an access token.
     *
     * @param DateInterval $accessTokenTTL
     * @param ClientEntityInterface $client
     * @param string|null $userIdentifier
     * @param array $scopes
     * @return AccessTokenEntityInterface
     * @throws OAuthException
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    protected function issueAccessToken(DateInterval $accessTokenTTL, 
                                        ClientEntityInterface $client,
                                        ?string $userIdentifier,
                                        array $scopes = []) : AccessTokenEntityInterface
    {
        $maxGenerationAttempts = self::MAX_RANDOM_TOKEN_GENERATION_ATTEMPTS;

        $accessToken = $this->accessTokenModel->getNewToken($client, $scopes, $userIdentifier);
        $accessToken->setExpiryDateTime((new DateTimeImmutable())->add($accessTokenTTL));
        $accessToken->setPrivateKey($this->privateKey);

        while($maxGenerationAttempts-- > 0)
        {
            $accessToken->setIdentifier($this->generateUniqueIdentifier());
            try {
                $this->accessTokenModel->persistNewAccessToken($accessToken);
                return $accessToken;
            } 
            catch (UniqueTokenIdentifierConstraintViolationException $e) 
            {
                if ($maxGenerationAttempts === 0)
                {
                    throw $e;
                }
            }
        }
    }

    /**
     * Issue a refresh token.
     *
     * @param AccessTokenEntityInterface $accessToken
     * @return RefreshTokenEntityInterface|null
     * @throws OAuthException
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    protected function issueRefreshToken(AccessTokenEntityInterface $accessToken) : ?RefreshTokenEntityInterface
    {
        $refreshToken = $this->refreshTokenModel->getNewRefreshToken();
        if ($refreshToken === null)
        {
            return null;
        }

        $refreshToken->setExpiryDateTime((new DateTimeImmutable())->add($this->refreshTokenTTL));
        $refreshToken->setAccessToken($accessToken);

        $maxGenerationAttempts = self::MAX_RANDOM_TOKEN_GENERATION_ATTEMPTS;

        while($maxGenerationAttempts-- > 0)
        {
            $refreshToken->setIdentifier($this->generateUniqueIdentifier());
            try {
                $this->refreshTokenModel->persistNewRefreshToken($refreshToken);
                return $refreshToken;
            }
            catch(UniqueTokenIdentifierConstraintViolationException $e)
            {
                if ($maxGenerationAttempts === 0)
                {
                    throw $e;
                }
            }
        }
    }

    /**
     * Generate a new unique identifier.
     *
     * @param integer $length
     * @return string
     */
    protected function generateUniqueIdentifier(int $length = 40) : string
    {
        $length = $length ?? $this->identifierLength;

        try {
            return bin2hex(random_bytes($length));
        }
        catch(TypeError $e)
        {
            throw OAuthException::serverError('An unexpected error has occurred', $e);
        }
        catch(Error $e)
        {
            throw OAuthException::serverError('An unexpected error has occurred', $e);
        }
        catch(Exception $e)
        {
            throw OAuthException::serverError('Could not generate a random string', $e);
        }
    }

    /**
     * Issue an auth code.
     *
     * @param DateInterval $authorizationCodeTTL
     * @param ClientEntityInterface $client
     * @param string $userIdentfier
     * @param string|null $redirectUri
     * @param array $scopes
     * 
     * @throws OAuthException
     * @throws UniqueAuthCodeIdentifierException
     * 
     * @return AuthorizationCodeEntityInterface
     */
    protected function issueAuthorizationCode(
        DateInterval $authorizationCodeTTL, 
        ClientEntityInterface $client, 
        string $userIdentfier, 
        ?string $redirectUri, 
        array $scopes = []) : AuthorizationCodeEntityInterface
    {
        $maxGenerationAttempts = self::MAX_RANDOM_TOKEN_GENERATION_ATTEMPTS;

        $authorizationCode = $this->authorizationCodeModel->getNewAuthorizationCode();
        $authorizationCode->setExpiryDateTime((new DateTimeImmutable())->add($authorizationCodeTTL));
        $authorizationCode->setClient($client);
        $authorizationCode->setUserIdentifier($userIdentfier);
        if ($redirectUri !== null)
        {
            $authorizationCode->setRedirectUri($redirectUri);
        }

        foreach($scopes as $scope)
        {
            $authorizationCode->addScope($scope);
        }

        while($maxGenerationAttempts-- > 0)
        {
            $authorizationCode->setIdentifier($this->generateUniqueIdentifier());
            try {
                $this->authorizationCodeModel->persistNewAuthorizationCode($authorizationCode);

                return $authorizationCode;
            } 
            catch(UniqueAuthorizationCodeIdentifierException $e)
            {
                if ($maxGenerationAttempts === 0) 
                    return $e;
            }
        }
    }

///////////////////////////////////////////////
// Commpon.
///////////////////////////////////////////////

    /**
     * Get client model.
     *
     * @param string $clientId
     * @return ClientEntityInterface
     */
    protected function getClientEntity(string $clientId, ServerRequestInterface $request) : ClientEntityInterface
    {
        $client = null;
        try {
            $client = $this->clientModel->getClientEntity($clientId);
            if ($client instanceof ClientEntityInterface === false)
            {
                throw new Exception();
            }
        } 
        catch (Exception $e)
        {
            $this->eventDispatcher()->dispatch(new RequestEvent(ClientAuthenticationFailedInterface::EVENT_NAME, $request));
            throw OAuthException::invalidClient($request);
        }
        finally
        {
        }
        return $client;
    }

    /**
     * Validate redirectUri from the request.
     *
     * @param string|null $redirectUri
     * @param ClientEntityInterface $client
     * @param ServerRequestInterface $request
     * @throws OAuthException
     * @return void
     */
    protected function validateRedirectUri(?string $redirectUri, ClientEntityInterface $client, ServerRequestInterface $request) : void
    {
        try {
            if ($redirectUri === null) 
            {
                if (empty($client->getRedirectUri()))
                {
                    throw new Exception();
                }
            }
            else 
            {
                if (strcmp($client->getRedirectUri(), $redirectUri) !== 0)
                {
                    throw new Exception();
                }                    
            }
        } 
        catch(Exception $e) 
        {
            $this->eventDispatcher()->dispatch(new RequestEvent(ClientAuthenticationFailedInterface::EVENT_NAME, $request));
            throw OAuthException::invalidClient($request);
        }
    }

///////////////////////////////////////////////
// Access Token.
///////////////////////////////////////////////

    /**
     * {@inheritDoc}
     */
    public function canRespondToAccessTokenRequest(ServerRequestInterface $request) : bool
    {
        if (is_null($this->grantTypeParame))
        {
            $this->setGrantTypeParame($request, $this->getGrantTypeParameClassName());
        }

        $params = (array) $request->getParsedBody();
        return (array_key_exists(GrantTypeParame::GRANT_TYPE, $params) 
                && $this->grantTypeParame->grantType === $this->getGrantType());
    }

    /**
     * Validate the client.
     *
     * @param ServerRequestInterface $request
     * @return ClientEntityInterface
     */
    protected function validateClient(ServerRequestInterface $request) : ClientEntityInterface
    {
        $clientId = $this->getGrantTypeParame()->clientId;
        $clientSecret = $this->getGrantTypeParame()->clientSecret;
        if ($this->clientModel->validateClient($clientId, $clientSecret, $this->getGrantType()) === false)
        {
            $this->eventDispatcher()->dispatch(new RequestEvent(ClientAuthenticationFailedInterface::EVENT_NAME, $request));
            throw OAuthException::invalidClient($request);
        }

        $client = $this->getClientEntity($clientId, $request);
        $redirectUri = $this->getGrantTypeParame()->redirectUri;
        if ($redirectUri !== null)
        {
            $this->validateRedirectUri($redirectUri, $client, $request);
        }

        return $client;
    }

///////////////////////////////////////////////

    /**
     * {@inheritDoc}
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request) : AuthorizationRequest
    {
        throw new LogicException('This grant cannot validate an authorization request');
    }

    /**
     * {@inheritDoc}
     */
    public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest) : ResponseTypeInterface
    {
        throw new LogicException('This grant cannot complete an authorization request');
    }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @param ResponseTypeInterface $responseType
     * @return ResponseTypeInterface
     */
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseTypeInterface $responseType): ResponseTypeInterface
    {
        throw new LogicException('This grant does not used this method');
    }
}