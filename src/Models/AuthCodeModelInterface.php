<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Models;

use BurningCloudSystem\OAuth2\Server\Entities\AuthCodeEntityInterface;

interface AuthCodeModelInterface
{
    /**
     * Creates a new AuthCode
     *
     * @return AuthCodeEntityInterface
     */
    public function getNewAuthCode() : AuthCodeEntityInterface;

    /**
     * Persists a new auth code to permanent storage.
     *
     * @param AuthCodeEntityInterface $authCodeEntity
     * 
     * 
     * 
     * @return void
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity) : void;

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

