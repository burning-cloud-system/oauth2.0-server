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
use BurningCloudSystem\OAuth2\Server\Models\AuthCodeModelInterface;
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
     * @var AuthorizationParame
     */
    private AuthorizationParame $requestParame;

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
    public function __construct(AuthCodeModelInterface $authCodeModel, ?DateInterval $authCodeTTL = null)
    {
        $this->requestParame = new AuthorizationParame();

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
     * Return the grant identifier that can be used in matching up requests.
     *
     * @return string
     */
    public function getIdentifier() : string
    {
        return 'authorization_code';
    }

    /**
     * The grant type should return true if it is able to response to an request
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function canRespondToRequest(ServerRequestInterface $request) : bool
    {
        $this->requestParame->bindParame($request);

        return $this->requestParame->responseType === 'code' && !is_null($this->requestParame->clientId);
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
    public function validateRequest(ServerRequestInterface $request) : AuthorizationRequest
    {
        // check client
        if ($this->requestParame->clientId === null) 
        {
            throw OAuthException::invalidRequest(AuthorizationParame::CLIENT_ID);
        }
        $client = $this->getClientEntity($this->requestParame->clientId);
        if ($client == null || $client instanceof ClientEntityInterface === false)
        {
            throw OAuthException::invalidClient($request);
        }

        // check redirect uri
        if ($this->requestParame->redirectUri !== null)
        {
            $this->validateRedirectUri($this->requestParame->redirectUri, $client, $request);
        } elseif (empty($client->getRedirectUri()))
        {
            throw OAuthException::invalidClient($request);
        }
        $this->requestParame->redirectUri ?? $client->getRedirectUri();

        (count($this->requestParame->scopes) === 0) 
            && !is_null($this->defaultScope) 
            && ($this->requestParame->scopes[] = $this->defaultScope);
        $scopes = $this->validateScopes($this->requestParame->scopes, $this->requestParame->redirectUri);

        if ($this->requestParame->codeChallenge !== null) 
        {
            if (array_key_exists($this->requestParame->codeChallengeMethod, $this->codeChallengeVerifiers) === false)
            {
                throw OAuthException::invalidRequest(
                    AuthorizationParame::CODE_CHALLENGE_METHOD,
                    'Code challenge method must be one of ' . 
                        implode(', ', 
                            array_map(function ($method) { return '`'.$method.'`'; },
                                array_keys($this->codeChallengeVerifiers))));
            }

            // RFC-7636, section 4.2.
            if (preg_match('/^[A-Za-z0-9-._~]{43,128}$/', $this->requestParame->codeChallenge) !== 1)
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
        $authorizationRequest->setGrantTypeId($this->getIdentifier());
        $authorizationRequest->setClient($client);
        $authorizationRequest->setRedirectUri($this->requestParame->redirectUri);
        if ($this->requestParame->state !== null) {
            $authorizationRequest->setState($this->requestParame->state);
        }
        $authorizationRequest->setScopes($scopes);
        if ($this->requestParame->codeChallenge !== null)
        {
            $authorizationRequest->setCodeChallenge($this->requestParame->codeChallenge);
            $authorizationRequest->setCodeChallengMethod($this->requestParame->codeChallengeMethod);
        }

        return $authorizationRequest;
    }

    /**
     * {@inheritDoc}
     *
     * @param AuthorizationRequest $authorizationRequest
     * @return ResponseTypeInterface
     */
    public function completeRequest(AuthorizationRequest $authorizationRequest) : ResponseTypeInterface
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

}