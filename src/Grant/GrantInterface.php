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
use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;
use BurningCloudSystem\OAuth2\Server\Models\AccessTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ScopeModelInterface;
use BurningCloudSystem\OAuth2\Server\Request\AuthorizationRequest;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use DateInterval;
use Defuse\Crypto\Key;
use Psr\Http\Message\ServerRequestInterface;


interface GrantInterface
{
    /**
     * Return the grant type that can be used in matching up requests.
     *
     * @return string|null
     */
    public function getGrantType() : ?string;

    /**
     * Return the response type that can be used in matching up requests.
     *
     * @return string|null
     */
    public function getResponseType() : ?string;

    /**
     * The grant type should return true if it is able to response to an request
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function canRespondToAuthorizationRequest(ServerRequestInterface $request) : bool;

    /**
     * If the grant can respond to an request this method should be called to validate the parameters of
     * the request.
     *
     * If the validation is successful an Request object will be returned. This object can be safely
     * serialized in a user's session, and can be used during user authentication and authorization.
     *
     * @param ServerRequestInterface $request
     * @return AuthorizationRequest
     * @throws OAuthException
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request) : AuthorizationRequest;

    /**
     * Once a user has authenticated and authorized the client the grant can complete the authorization request.
     * The AuthorizationRequest object's $userId property must be set to the authenticated user and the
     * $authorizationApproved property must reflect their desire to authorize or deny the client.
     *
     * @param AuthorizationRequest $authorizationRequest
     * @return ResponseTypeInterface
     * @throws OAuthException
     */
    public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest) : ResponseTypeInterface;
    
    /**
     * The grant type should return true if it is able to respond to this request.
     *
     * For example most grant types will check that the $_POST['grant_type'] property matches it's identifier property.
     *
     * @param ServerRequestInterface $request
     * @return bool
     * @throws OAuthException
     */
    public function canRespondToAccessTokenRequest(ServerRequestInterface $request) : bool;

    /**
     * Respond to an incoming request.
     *
     * @param ServerRequestInterface $request
     * @param ResponseTypeInterface $responseType
     * @param DateInterval $accessTokenTTL
     * @return ResponseTypeInterface
     */
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseTypeInterface $responseType) : ResponseTypeInterface;

    /**
     * Set the client model.
     *
     * @param ClientModelInterface $clientModel
     * @return void
     */
    public function setClientModel(ClientModelInterface $clientModel) : void;

    /**
     * Set the access token model.
     *
     * @param AccessTokenModelInterface $accessTokenModel
     * @return void
     */
    public function setAccessTokenModel(AccessTokenModelInterface $accessTokenModel) : void;

    /**
     * Set the scope model.
     *
     * @param ScopeModelInterface $scopeModel
     * @return void
     */
    public function setScopeModel(ScopeModelInterface $scopeModel) : void;

    /**
     * Set the default scope.
     *
     * @param string $defaultScope
     * @return void
     */
    public function setDefaultScope(string $defaultScope) : void;

    /**
     * Set access token TTL.
     *
     * @param DateInterval $accessTokenTTL
     * @return void
     */
    public function setAccessTokenTTL(DateInterval $accessTokenTTL) : void;

    /**
     * Set refresh token TTL.
     *
     * @param DateInterval $refreshTokenTTL
     * @return void
     */
    public function setRefreshTokenTTL(DateInterval $refreshTokenTTL) : void;

    /**
     * Set the path to the private key.
     *
     * @param string|CryptKey $privateKey
     * @return void
     */
    public function setPrivateKey($privateKey) : void;

    /**
     * Set the encryption key.
     *
     * @param Key|null $key
     * @return void
     */
    public function setEncryptionKey(?Key $key = null) : void;

    /**
     * Get response type parame class name.
     *
     * @return string
     */
    public function getResponseTypeParameClassName() : string;

    /**
     * Get grant type parame class name.
     *
     * @return string
     */
    public function getGrantTypeParameClassName() : string;
}

