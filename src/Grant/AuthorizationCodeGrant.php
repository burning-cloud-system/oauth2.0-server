<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Grant;

use BurningCloudSystem\OAuth2\Server\CodeChallengeVerifiers\CodeChallengeVerifierInterface;
use BurningCloudSystem\OAuth2\Server\Entities\UserEntityInterface;
use BurningCloudSystem\OAuth2\Server\Request\AuthorizationRequest;
use BurningCloudSystem\OAuth2\Server\CodeChallengeVerifiers\PlainVerifier;
use BurningCloudSystem\OAuth2\Server\CodeChallengeVerifiers\S256Verifier;
use BurningCloudSystem\OAuth2\Server\Event\Listener\AccessTokenIssuedInterface;
use BurningCloudSystem\OAuth2\Server\Event\RequestEvent;
use BurningCloudSystem\OAuth2\Server\Exception\NotImplementedException;
use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;
use BurningCloudSystem\OAuth2\Server\Models\AccessTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\AuthorizationCodeModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\RefreshTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ScopeModelInterface;
use BurningCloudSystem\OAuth2\Server\Request\Parame\AuthorizationGrantTypeParame;
use BurningCloudSystem\OAuth2\Server\Request\Parame\AuthorizationResponseTypeParame;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\RedirectResponse;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use DateInterval;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationCodeGrant extends AbstractAuthorizationCodeGrant implements GrantInterface
{
    // use AuthorizeGrantTrait;

    /**
     * @var DateInterval
     */
    private DateInterval $authorizationCodeTTL;

    /**
     * @var CodeChallengeVerifierInterface[]
     */
    private array $codeChallengeVerifiers = [];

    // /**
    //  * @var bool
    //  */
    // private bool $requireCodeChallengeForPublicClients = true;

    /**
     * construct.
     *
     * @param string $privateKey
     * @param string $encryptionKey
     * @param ClientModelInterface $clientModel
     * @param ScopeModelInterface $scopeModel
     * @param AuthorizationCodeModelInterface $authorizationCodeModel
     * @param AccessTokenModelInterface $accessTokenModel
     * @param RefreshTokenModelInterface $refreshTokenModel
     * @param DateInterval|null $authorizationCodeTTL
     */
    public function __construct(string $privateKey, 
                                string $encryptionKey, 
                                ClientModelInterface $clientModel,
                                ScopeModelInterface $scopeModel, 
                                AuthorizationCodeModelInterface $authorizationCodeModel, 
                                AccessTokenModelInterface $accessTokenModel, 
                                RefreshTokenModelInterface $refreshTokenModel, 
                                ?DateInterval $authorizationCodeTTL = null)
    {
        parent::__construct($privateKey, $encryptionKey, $clientModel, $scopeModel, $accessTokenModel);

        $this->setAuthorizationCodeModel($authorizationCodeModel);
        $this->setRefreshTokenModel($refreshTokenModel);

        $this->authorizationCodeTTL = $authorizationCodeTTL ?? new DateInterval('PT10M');
        $this->setRefreshTokenTTL(new DateInterval('P1M'));

        if (in_array('sha256', hash_algos(), true))
        {
            $s256 = new S256Verifier();
            $this->codeChallengeVerifiers[$s256->getMethod()] = $s256;
        }
        $plain = new PlainVerifier();
        $this->codeChallengeVerifiers[$plain->getMethod()] = $plain;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier() : string
    {
        return 'authorization_code';
    }

    /**
     * {@inheritDoc}
     *
     * @return string|null
     */
    public function getGrantType(): ?string
    {
        return 'authorization_code';
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseType() : ?string
    {
        return 'code';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getResponseTypeParameClassName(): string
    {
        return AuthorizationResponseTypeParame::class;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getGrantTypeParameClassName(): string
    {
        return AuthorizationGrantTypeParame::class;
    }

    /**
     * {@inheritDoc}
     *
     * @return AuthorizationResponseTypeParame
     */
    public function getResponseTypeParame() : AuthorizationResponseTypeParame
    {
        return parent::getResponseTypeParame();
    }

    /**
     * {@inheritDoc}
     *
     * @return AuthorizationGrantTypeParame
     */
    public function getGrantTypeParame() : AuthorizationGrantTypeParame
    {
        return parent::getGrantTypeParame();
    }

    // /**
    //  * Disable the requirement for a code challenge for public clients.
    //  *
    //  * @return void
    //  */
    // public function disableRequireCodeChallengeForPublicClients() : void
    // {
    //     $this->requireCodeChallengeForPublicClients = false;
    // }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return AuthorizationRequest
     * @throws OAuthException
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request) : AuthorizationRequest
    {
        if (is_null($this->getResponseTypeParame()->clientId))
        {
            throw OAuthException::invalidRequest(AuthorizationResponseTypeParame::CLIENT_ID);
        }

        $client = $this->getClientEntity($this->getResponseTypeParame()->clientId, $request);

        $redirectUri = $this->getResponseTypeParame()->redirectUri;
        $this->validateRedirectUri($redirectUri, $client, $request);
        $redirectUri = $redirectUri ?? $client->getRedirectUri();

        $state = $this->getResponseTypeParame()->state;
        $scopes = $this->validateScopes($this->getResponseTypeParame()->scopes, $redirectUri);

        $authorizationRequest = new AuthorizationRequest();
        $authorizationRequest->setGrantTypeId($this->getIdentifier());
        $authorizationRequest->setClient($client);
        $authorizationRequest->setRedirectUri($redirectUri);
        $state !== null && $authorizationRequest->setState($state);
        $authorizationRequest->setScopes($scopes);

        if ($this->getResponseTypeParame()->codeChallenge !== null)
        {
            if (array_key_exists($this->getResponseTypeParame()->codeChallengeMethod, $this->codeChallengeVerifiers) === false)
            {
                throw OAuthException::invalidRequest(
                    AuthorizationResponseTypeParame::CODE_CHALLENGE_METHOD,
                    'Code challenge method must be one of ' . 
                        implode(', ', 
                            array_map(function ($method) { return '`'.$method.'`'; },
                                array_keys($this->codeChallengeVerifiers))));
            }

            // Validate code_challenge according to RFC-7636
            // @see: https://tools.ietf.org/html/rfc7636#section-4.2
            if (preg_match('/^[A-Za-z0-9-._~]{43,128}$/', $this->getResponseTypeParame()->codeChallenge) !== 1)
            {
                throw OAuthException::invalidRequest(
                    AuthorizationResponseTypeParame::CODE_CHALLENGE,
                    'Code challenge must follow the specifications of RFC-7636.');
            }

            $authorizationRequest->setCodeChallenge($this->getResponseTypeParame()->codeChallenge);
            $authorizationRequest->setCodeChallengMethod($this->getResponseTypeParame()->codeChallengeMethod);
        }
        elseif ($this->requireCodeChallengeForPublicClients && !$client->isConfidential())
        {
            throw OAuthException::invalidRequest(
                AuthorizationResponseTypeParame::CODE_CHALLENGE,
                'Code challenge must be provided for public clients');
        }

        return $authorizationRequest;
    }

    /**
     * {@inheritDoc}
     *
     * @param AuthorizationRequest $authorizationRequest
     * @return ResponseTypeInterface
     * @throws OAuthException
     
     */
    public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest) : ResponseTypeInterface
    {
        if ($authorizationRequest->getUser() instanceof UserEntityInterface === false)
        {
            throw new LogicException('An instance of UserEntityInterface should be set on the AuthorizationRequest');
        }

        $redirectUri = $authorizationRequest->getRedirectUri();

        if ($authorizationRequest->isAuthorizationApproved() === true)
        {
            $authorizationCode = $this->issueAuthorizationCode(
                                            $this->authorizationCodeTTL,
                                            $authorizationRequest->getClient(),
                                            $authorizationRequest->getUser()->getIdentfier(),
                                            $authorizationRequest->getRedirectUri(),
                                            $authorizationRequest->getScopes());

            $payload = [
                'authorization_code_id' => $authorizationCode->getIdentifier(),
                'client_id'             => $authorizationCode->getClient()->getIdentifier(),
                'user_id'               => $authorizationCode->getUserIdentifier(),
                'redirect_uri'          => $authorizationCode->getRedirectUri(),
                'scopes'                => $authorizationCode->getScopes(),
                'expire_time'           => $authorizationCode->getExpiryDateTime()->getTimestamp(),
                'code_challenge'        => $authorizationRequest->getCodeChallenge(),
                'code_challenge_method' => $authorizationRequest->getCodeChallengeMethod(),
            ];

            $jsonPayload = json_encode($payload);

            if ($jsonPayload === false)
            {
                throw new LogicException('An error was encountered when JSON encoding the authorization request response');
            }

            $response = new RedirectResponse();
            $response->setRedirectUri(
                $this->makeRedirectUri(
                    $redirectUri, [
                        'code'  => $this->encrypt($jsonPayload),
                        'state' => $authorizationRequest->getState()
                    ]));

            return $response;
        }

        throw OAuthException::accessDenied(
            'The user denied the request',
            $this->makeRedirectUri(
                $redirectUri,
                [ 'state' => $authorizationRequest->getState() ]));
    }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return void
     * @throws OAuthException
     */
    public function validateAccessTokenRequest(ServerRequestInterface $request): void
    {

        $encryptedAuthorizationCode = $this->getGrantTypeParame()->code;
        if ($encryptedAuthorizationCode === null)
        {
            throw OAuthException::invalidRequest('code');
        }

        try {
            $authorizationCodePayload = json_decode($this->decrypt($encryptedAuthorizationCode));

            // $this->validateAuthorizationCode($authCodePayload, $client, $request);

            $scopes = $this->scopeModel->finalizeScopes();

        } 
        catch (LogicException $e) 
        {

        }


        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @return ResponseTypeInterface
     * @throws OAuthException
     */
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseTypeInterface $responseType): ResponseTypeInterface
    {
        $client = $this->getClientEntity($this->getGrantTypeParame()->clientId, $request);

        // Only validate the client if it is confidential
        if ($client->isConfidential())
        {

        }


        $client = null;
        $authCodePayload = null;
        $scopes = null;

        $accessToken = $this->issueAccessToken($this->accessTokenTTL, $client, $authCodePayload->user_id, $scopes);
        $this->eventDispatcher()->dispatch(new RequestEvent(AccessTokenIssuedInterface::EVENT_NAME, $request));


    }

}