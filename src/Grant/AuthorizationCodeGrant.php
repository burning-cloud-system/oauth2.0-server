<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Grant;

use BurningCloudSystem\OAuth2\Server\Entities\ClientEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\UserEntityInterface;
use BurningCloudSystem\OAuth2\Server\Request\AuthorizationRequest;
use BurningCloudSystem\OAuth2\Server\CodeChallengeVerifiers\PlainVerifier;
use BurningCloudSystem\OAuth2\Server\CodeChallengeVerifiers\S256Verifier;
use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;
use BurningCloudSystem\OAuth2\Server\Models\AccessTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\AuthCodeModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\RefreshTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ScopeModelInterface;
use BurningCloudSystem\OAuth2\Server\Request\Parame\AuthorizationParame;
use BurningCloudSystem\OAuth2\Server\Response\RedirectResponse;
use BurningCloudSystem\OAuth2\Server\Response\ResponseTypeInterface;
use DateInterval;
use DateTimeImmutable;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationCodeGrant extends AbstractGrant
{
    use AuthorizeGrantTrait;

    /**
     * @var int
     */
    private int $identifierLength = 0;

    /**
     * @var int
     */
    private int $defaultIdentifierLength = 40;

    /**
     * @var DateInterval
     */
    private DateInterval $authCodeTTL;

    /**
     * @var array
     */
    private array $codeChallengeVerifiers = [];

    /**
     * @var bool
     */
    private bool $requireCodeChallengeForPublicClients = true;

    /**
     * construct.
     * 
     * @var void
     */
    public function __construct(string $privateKey, string $encryptionKey, 
        ClientModelInterface $clientModel, ScopeModelInterface $scopeModel, AuthCodeModelInterface $authCodeModel, AccessTokenModelInterface $accessTokenModel, RefreshTokenModelInterface $refreshTokenModel, 
        ?DateInterval $authCodeTTL = null)
    {
        $this->setAuthCodeModel($authCodeModel);
        $this->authCodeTTL = $authCodeTTL ?? new DateInterval('PT10M');

        if (in_array('sha256', hash_algos(), true))
        {
            $s256 = new S256Verifier();
            $this->codeChallengeVerifiers[$s256->getMethod()] = $s256;
        }
        $plain = new PlainVerifier();
        $this->codeChallengeVerifiers[$plain->getMethod()] = $plain;
    }

    /**
     * Set identifier length.
     *
     * @param integer $identifierLength
     * @return void
     */
    public function setIdentifierLength(int $identifierLength) : void
    {
        $this->identifierLength = $identifierLength;
    }

    /**
     * Get identifier length.
     *
     * @return integer
     */
    protected function getIdentifierLength(): int
    {
        return $this->identifierLength <= 0 ? $this->defaultIdentifierLength : $this->identifierLength;
    }

    /**
     * Disable the requirement for a code challenge for public clients.
     *
     * @return void
     */
    public function disableRequireCodeChallengeForPublicClients() : void
    {
        $this->requireCodeChallengeForPublicClients = false;
    }

    /**
     * {@inheritDoc}
     */
    public function getGrantType() : string
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
     * If the grant can respond to an request this method should be called to validate the parameters of
     * the request.
     *
     * If the validation is successful an Request object will be returned. This object can be safely
     * serialized in a user's session, and can be used during user authentication and authorization.
     *
     * @param ServerRequestInterface $request
     * 
     * @throws OAuthException
     *
     * @return AuthorizationRequest
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request) : AuthorizationRequest
    {
        $authorizationParame = new AuthorizationParame();
        $authorizationParame->bindParame($request);

        // check client
        if ($authorizationParame->clientId === null) 
        {
            throw OAuthException::invalidRequest(AuthorizationParame::CLIENT_ID);
        }
        $client = $this->getClientEntity($authorizationParame->clientId);
        if ($client == null || $client instanceof ClientEntityInterface === false)
        {
            throw OAuthException::invalidClient($request);
        }

        // check redirect uri
        if ($authorizationParame->redirectUri !== null)
        {
            $this->validateRedirectUri($authorizationParame->redirectUri, $client, $request);
        } elseif (empty($client->getRedirectUri()))
        {
            throw OAuthException::invalidClient($request);
        }
        $authorizationParame->redirectUri ?? $client->getRedirectUri();

        (count($authorizationParame->scopes) === 0) 
            && !is_null($this->defaultScope) 
            && ($authorizationParame->scopes[] = $this->defaultScope);
        $scopes = $this->validateScopes($authorizationParame->scopes, $authorizationParame->redirectUri);

        if ($authorizationParame->codeChallenge !== null) 
        {
            if (array_key_exists($authorizationParame->codeChallengeMethod, $this->codeChallengeVerifiers) === false)
            {
                throw OAuthException::invalidRequest(
                    AuthorizationParame::CODE_CHALLENGE_METHOD,
                    'Code challenge method must be one of ' . 
                        implode(', ', 
                            array_map(function ($method) { return '`'.$method.'`'; },
                                array_keys($this->codeChallengeVerifiers))));
            }

            // RFC-7636, section 4.2.
            if (preg_match('/^[A-Za-z0-9-._~]{43,128}$/', $authorizationParame->codeChallenge) !== 1)
            {
                throw OAuthException::invalidRequest(
                    AuthorizationParame::CODE_CHALLENGE,
                    'Code challenge must follow the specifications of RFC-7636.');
            }
        } 
        elseif ($this->requireCodeChallengeForPublicClients && !$client->isConfidential())
        {
            throw OAuthException::invalidRequest(
                AuthorizationParame::CODE_CHALLENGE,
                'Code challenge must be provided for public clients');
        }

        $authorizationRequest = new AuthorizationRequest();
        $authorizationRequest->setGrantTypeId($this->getGrantType());
        $authorizationRequest->setClient($client);
        $authorizationRequest->setRedirectUri($authorizationParame->redirectUri);
        if ($authorizationParame->state !== null) {
            $authorizationRequest->setState($authorizationParame->state);
        }
        $authorizationRequest->setScopes($scopes);
        if ($authorizationParame->codeChallenge !== null)
        {
            $authorizationRequest->setCodeChallenge($authorizationParame->codeChallenge);
            $authorizationRequest->setCodeChallengMethod($authorizationParame->codeChallengeMethod);
        }

        return $authorizationRequest;
    }

    /**
     * {@inheritDoc}
     *
     * @param AuthorizationRequest $authorizationRequest
     * @return ResponseTypeInterface
     */
    public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest) : ResponseTypeInterface
    {
        if ($authorizationRequest->getUser() instanceof UserEntityInterface === false)
        {
            throw new LogicException('An instance of UserEntityInterface should be set on the AuthorizationRequest');
        }

        $finalRedirectUri = $authorizationRequest->getRedirectUri();

        if ($authorizationRequest->isAuthorizationApproved() === true)
        {
            $authCode = $this->issueAuthCode(
                            $this->authCodeTTL,
                            $authorizationRequest->getClient(),
                            $authorizationRequest->getUser()->getIdentfier(),
                            $authorizationRequest->getRedirectUri(),
                            $authorizationRequest->getScopes());

            $payload = [
                'client_id'             => $authCode->getClient()->getIdentfier(),
                'redirect_uri'          => $authCode->getRedirectUri(),
                'auth_code_id'          => $authCode->getIdentifier(),
                'scopes'                => $authCode->getScopes(),
                'user_id'               => $authCode->getUserIdentifier(),
                'expire_time'           => (new DateTimeImmutable())->add($this->authCodeTTL)->getTimestamp(),
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
                    $finalRedirectUri, [
                        'code'  => $this->encrypt($jsonPayload),
                        'state' => $authorizationRequest->getState()
                    ]));

            return $response;
        }

        throw OAuthException::accessDenied(
            'The user denied the request',
            $this->makeRedirectUri(
                $finalRedirectUri,
                [ 'state' => $authorizationRequest->getState() ]));
    }

    /**
     * {@inheritDoc}
     */
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseTypeInterface $responseType, DateInterval $accessTokenTTL) : ResponseTypeInterface
    {

    }

}