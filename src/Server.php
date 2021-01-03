<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
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
        ClientModelInterface $clientModel,
        CryptKey $privateKey,
        Key $encryptionKey)
    {
        $this->clientModel = $clientModel;
        $this->privateKey = $privateKey;
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
    public function validateRequest(ServerRequestInterface $request) : AuthorizationRequest
    {
        foreach($this->grantTypes as $grantType) 
        {
            if ($grantType instanceof GrantTypeInterface && $grantType->canRespondToRequest(($request)))
            {
                return $grantType->validateRequest($request);
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
    public function completeRequest(AuthorizationRequest $authorizationRequest, ResponseInterface $response) : ResponseInterface
    {
        return $this->grantTypes[$authorizationRequest->getGrantTypeId()]
                        ->completeRequest($authorizationRequest)
                        ->generateHttpResponse($response);

    }

    public function respond(ServerRequestInterface $request, ResponseInterface $response)
    {

        // TODO Exception.
        // throw OAuthServerException::unsupportedGrantType();
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