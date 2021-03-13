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
use BurningCloudSystem\OAuth2\Server\Entities\AuthCodeEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\ClientEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\ScopeEntityInterface;
use BurningCloudSystem\OAuth2\Server\Models\AuthCodeModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ScopeModelInterface;
use BurningCloudSystem\OAuth2\Server\Request\AuthorizationRequest;
use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;
use BurningCloudSystem\OAuth2\Server\Exception\UniqueAuthCodeIdentifierException;
use BurningCloudSystem\OAuth2\Server\Response\ResponseTypeInterface;
use DateInterval;
use DateTimeImmutable;
use Error;
use Exception;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TypeError;

abstract class AbstractGrant implements GrantTypeInterface
{
    use CryptTrait;

    const MAX_RANDOM_TOKEN_GENERATION_ATTEMPTS = 10;

    /**
     * @var ClientModelInterface
     */
    protected ClientModelInterface $clientModel;

    /**
     * @var ScopeModelInterface
     */
    protected ScopeModelInterface $scopeModel;

    /**
     * @var AuthCodeModelInterface
     */
    protected AuthCodeModelInterface $authCodeModel;

    /**
     * @var CryptKey
     */
    protected CryptKey $privateKey;

    /**
     * Default scope.
     * 
     * @var string|null
     */
    protected ?string $defaultScope = null;

    /**
     * Get identifier length.
     *
     * @return integer
     */
    abstract protected function getIdentifierLength() : int;

    /**
     * Set client model.
     *
     * @param ClientModelInterface $clientModel
     * @return void
     */
    public function setClientModel(ClientModelInterface $clientModel) : void
    {
        $this->clientModel = $clientModel;
    }

    /**
     * Get client model.
     *
     * @param string $clientId
     * @return ClientEntityInterface
     */
    protected function getClientEntity(string $clientId) : ClientEntityInterface
    {
        $client = null;
        try {
            $client = $this->clientModel->getClientEntity($clientId);
        }
        finally
        {
        }
        return $client;
    }

    protected function getClientCredentials(ServerRequestInterface $request)
    {

    }

    /**
     * Set scope model.
     * 
     * @param ScopeModelInterface $scopeModel
     * @return void
     */
    public function setScopeModel(ScopeModelInterface $scopeModel) : void
    {
        $this->scopeModel = $scopeModel;
    }

    /**
     * Set authorize code model.
     *
     * @param AuthCodeModelInterface $authCodeModel
     * @return void
     */
    public function setAuthCodeModel(AuthCodeModelInterface $authCodeModel) : void
    {
        $this->authCodeModel = $authCodeModel;
    }

    /**
     * Set the private key.
     *
     * @param CryptKey $privateKey
     * @return void
     */
    public function setPrivateKey(CryptKey $privateKey) : void
    {
        $this->privateKey = $privateKey;
    }

    /**
     * Set default scope.
     *
     * @param string $scope
     * @return void
     */
    public function setDefaultScope(string $scope) : void
    {
        $this->defaultScope = $scope;
    }

    /**
     * Validate redirectUri from the request.
     *
     * @param string $redirectUri
     * @param ClientEntityInterface $client
     * @param ServerRequestInterface $request
     * 
     * @throws OAuthException
     * 
     * @return void
     */
    protected function validateRedirectUri(string $redirectUri, ClientEntityInterface $client, ServerRequestInterface $request) : void
    {
        if (is_string($client->getRedirectUri()) && strcmp($client->getRedirectUri(), $redirectUri) !== 0)
        {
            throw OAuthException::invalidClient($request);
        }
    }

    /**
     * Validate scopes in the request.
     *
     * @param array $scopes
     * @param string|null $redirectUri
     * 
     * @throws OAuthException
     * 
     * @return array
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
     * Issue an auth code.
     *
     * @param DateInterval $authCodeTTL
     * @param ClientEntityInterface $client
     * @param string $userIdentfier
     * @param string|null $redirectUri
     * @param array $scopes
     * 
     * @throws OAuthException
     * @throws UniqueAuthCodeIdentifierException
     * 
     * @return AuthCodeEntityInterface
     */
    protected function issueAuthCode(DateInterval $authCodeTTL, ClientEntityInterface $client, string $userIdentfier, ?string $redirectUri, array $scopes = []) : AuthCodeEntityInterface
    {
        $maxGenerationAttempts = self::MAX_RANDOM_TOKEN_GENERATION_ATTEMPTS;

        $authCode = $this->authCodeModel->getNewAuthCode();
        $authCode->setExpiryDateTime((new DateTimeImmutable())->add($authCodeTTL));
        $authCode->setClient($client);
        $authCode->setUserIdentifier($userIdentfier);
        if ($redirectUri !== null)
        {
            $authCode->setRedirectUri($redirectUri);
        }

        foreach($scopes as $scope)
        {
            $authCode->addScope($scope);
        }

        while($maxGenerationAttempts-- > 0)
        {
            $authCode->setIdentifier($this->generateUniqueIdentifier($this->getIdentifierLength()));
            try {
                $this->authCodeModel->persistNewAuthCode($authCode);

                return $authCode;
            } 
            catch(UniqueAuthCodeIdentifierException $e)
            {
                if ($maxGenerationAttempts === 0) 
                    return $e;
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
     * {@inheritDoc}
     */
    public function canRespondToAuthorizationRequest(ServerRequestInterface $request) : bool
    {
        $params = $request->getQueryParams();
        return (array_key_exists('response_type', $params)
                && $params['response_type'] === $this->getResponseType()
                && isset($params['client_id']));
    }

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
     */
    public function canRespondToAccessTokenRequest(ServerRequestInterface $request) : bool
    {
        $params = (array) $request->getParsedBody();
        return (array_key_exists('grant_type', $params) 
                && $params['grant_type'] === $this->getGrantType());
    }
}