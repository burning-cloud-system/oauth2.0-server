<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2010 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server;

use BurningCloudSystem\OAuth2\Server\Crypt\CryptKey;
use BurningCloudSystem\OAuth2\Server\Grant\GrantTypeInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Request\AuthorizationRequest as RequestAuthorizationRequest;
use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;
use BurningCloudSystem\OAuth2\Server\Request\AuthorizationRequest;
use BurningCloudSystem\OAuth2\Server\Response\ResponseTypeInterface;
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
     * Grant Types
     *
     * @var array
     */
    protected $grantTypes = [];

    /**
     * @var CryptKey
     */
    protected CryptKey $privateKey;

    /**
     * @var Key
     */
    private Key $encryptionKey;

    /**
     * @var string|null
     */
    private ?string $defaultScope = null;


    public function __construct(
        string $privateKey,
        Key $encryptionKey)
    {
        $this->privateKey    = new CryptKey($privateKey);
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * Enable a grant type on the server.
     *
     * @param GrantTypeInterface $grantType
     * @return void
     */
    public function setGrantType(GrantTypeInterface $grantType) : void
    {
        $grantType->setClientModel($this->clientModel);

        $grantType->setDefaultScope($this->defaultScope);
        $grantType->setPrivateKey($this->privateKey);
        $grantType->setEncryptionKey($this->encryptionKey);

        $this->grantTypes[$grantType->getIdentifier()] = $grantType;
    }

    /**
     * Validate an request
     *
     * @param ServerRequestInterface $request
     * @throws OAuthServerException
     * @return AuthorizationRequest
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request) : AuthorizationRequest
    {
        foreach($this->grantTypes as $grantType) 
        {
            if ($grantType instanceof GrantTypeInterface && $grantType->canRespondToAuthorizationRequest(($request)))
            {
                return $grantType->validateAuthorizationRequest($request);
            }
        }

        throw OAuthException::unsupportedGrantType();
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
        return $this->grantTypes[$authorizationRequest->getGrantTypeId()]
                        ->completeAuthorizationRequest($authorizationRequest)
                        ->generateHttpResponse($response);
    }

    /**
     * Return an access token response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @throws OAuthException
     * @return ResponseInterface
     */
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        foreach($this->grantTypes as $grantType)
        {
            if ($grantType instanceof GrantTypeInterface && $grantType->canRespondToAccessTokenRequest($request))
            {
                $tokenResponse = $grantType->respondToAccessTokenRequest(
                    $request,
                    $this->getResponseType(),
                    null);
                if ($tokenResponse instanceof ResponseTypeInterface)
                {
                    return $tokenResponse->generateHttpResponse($response);
                }
            }
        }

        throw OAuthException::unsupportedGrantType();
    }

    /**
     * Set the default scope for the authorization server.
     *
     * @param string $defaultScope
     * @return void
     */
    public function setDefaultScope(string $defaultScope) : void
    {
        $this->defaultScope = $defaultScope;
    }
}