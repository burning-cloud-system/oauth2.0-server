<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server;

use BurningCloudSystem\OAuth2\Server\Crypt\CryptKey;
use BurningCloudSystem\OAuth2\Server\Grant\GrantInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Request\AuthorizationRequest as RequestAuthorizationRequest;
use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;
use BurningCloudSystem\OAuth2\Server\Request\AuthorizationRequest;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\AbstractResponseType;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use DateInterval;
use Defuse\Crypto\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server 
{
    /**
     * @var ClientModelInterface
     */
    private ClientModelInterface $clientModel;

    /**
     * Grants
     *
     * @var GrantInterface
     */
    protected $grant;

    /**
     * @var CryptKey
     */
    protected CryptKey $privateKey;

    /**
     * @var string|Key
     */
    private $encryptionKey;

    /**
     * @var string|null
     */
    private ?string $defaultScope = null;

    /**
     * @var ResponseTypeInterface
     */
    protected ResponseTypeInterface $responseType;

    /**
     * new server instance.
     *
     * @param string|CryptKey $privateKey
     * @param string|Key $encryptionKey
     * @param ResponseTypeInterface|null $responseType
     */
    public function __construct(
        $privateKey,
        $encryptionKey,
        ?ResponseTypeInterface $responseType = null)
    {
        if ($privateKey instanceof CryptKey === false)
        {
            $privateKey = new CryptKey((string) $privateKey);
        }
        $this->privateKey    = $privateKey;
        $this->encryptionKey = $encryptionKey;

        if ($responseType === null)
        {
            $responseType = new BearerTokenResponse();
        }
        else 
        {
            $responseType = clone $responseType;
        }
        $this->responseType = $responseType;
    }

    /**
     * Enable a grant on the server.
     * TODO.
     *
     * @param GrantInterface $grant
     * @return void
     */
    public function setGrantType(GrantInterface $grant) : void
    {
        // $grant->setClientModel($this->clientModel);
        $grant->setDefaultScope($this->defaultScope);
        $grant->setPrivateKey($this->privateKey);
        $grant->setEncryptionKey($this->encryptionKey);

        $this->grant = $grant;
    }

    /**
     * Validate an authorization request
     *
     * @param ServerRequestInterface $request
     * @return AuthorizationRequest
     * @throws OAuthServerException
     * @throws OAuthException
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request) : AuthorizationRequest
    {
        if ($this->grant instanceof GrantInterface && $this->grant->canRespondToAuthorizationRequest(($request)))
        {
            return $this->grant->validateAuthorizationRequest($request);
        }

        throw OAuthException::unsupportedGrant();
    }

    /**
     * Complete an authorization request
     *
     * @param AuthorizationRequest $authorizationRequest
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest, ResponseInterface $response) : ResponseInterface
    {
        return $this->grant->completeAuthorizationRequest($authorizationRequest)
                           ->generateHttpResponse($response);
    }

    /**
     * Return an access token response.
     * TODO.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @throws OAuthException
     * @return ResponseInterface
     */
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        if ($this->grant instanceof GrantInterface && $this->grant->canRespondToAccessTokenRequest($request))
        {
            $tokenResponse = $this->grant->respondToAccessTokenRequest($request, $this->getResponseType());
            
            if ($tokenResponse instanceof ResponseTypeInterface)
            {
                return $tokenResponse->generateHttpResponse($response);
            }
        }
        throw OAuthException::unsupportedGrant();
    }

    /**
     * Get the token type that grants will return in the HTTP response.
     *
     * @return ResponseTypeInterface
     */
    protected function getResponseType() : ResponseTypeInterface
    {
        $responseType = $this->responseType;
        if ($responseType instanceof AbstractResponseType)
        {
            $responseType->setPrivateKey($this->privateKey);
        }

        $responseType->setEncryptionKey($this->encryptionKey);

        return $responseType;
    }

    /**
     * Set the default scope for the authorization server.
     * TODO.
     *
     * @param string $defaultScope
     * @return void
     */
    public function setDefaultScope(string $defaultScope) : void
    {
        $this->defaultScope = $defaultScope;
    }
}