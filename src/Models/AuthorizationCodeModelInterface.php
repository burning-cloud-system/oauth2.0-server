<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Models;

use BurningCloudSystem\OAuth2\Server\Entities\AuthorizationCodeEntityInterface;

interface AuthorizationCodeModelInterface
{
    /**
     * Creates a new AuthorizationCode
     *
     * @return AuthorizationCodeEntityInterface
     */
    public function getNewAuthorizationCode() : AuthorizationCodeEntityInterface;

    /**
     * Persists a new authorization code to permanent storage.
     *
     * @param AuthorizationCodeEntityInterface $authorizationCodeEntity
     * @return void
     */
    public function persistNewAuthorizationCode(AuthorizationCodeEntityInterface $authorizationCodeEntity) : void;

    /**
     * Revoke an authorization code.
     *
     * @param string $codeId
     * @return void
     */
    public function revokeAuthorizationCode(string $codeId) : void;

    /**
     * Check if the authorization code has been revoked.
     *
     * @param string $codeId
     * @return boolean
     */
    public function isAuthorizationCodeRevoked(string $codeId) : bool;
}

