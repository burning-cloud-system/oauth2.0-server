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
     * Creates a new AuthCode
     *
     * @return AuthorizationCodeEntityInterface
     */
    public function getNewAuthorizationCode() : AuthorizationCodeEntityInterface;

    /**
     * Persists a new auth code to permanent storage.
     *
     * @param AuthorizationCodeEntityInterface $authorizationCodeEntity
     * @return void
     */
    public function persistNewAuthorizationCode(AuthorizationCodeEntityInterface $authorizationCodeEntity) : void;

    /**
     * Revoke an auth code.
     *
     * @param string $codeId
     * @return void
     */
    public function revokeAuthCode(string $codeId) : void;

    /**
     * Check if the auth code has been revoked.
     *
     * @param string $codeId
     * @return boolean
     */
    public function isAuthCodeRevoked(string $codeId) : bool;
}

