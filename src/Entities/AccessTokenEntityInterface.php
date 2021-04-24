<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Entities;

use BurningCloudSystem\OAuth2\Server\Crypt\CryptKey;

interface AccessTokenEntityInterface extends TokenInterface
{
    /**
     * Set a private key used to encrypt the access token.
     *
     * @param CryptKey $privateKey
     * @return void
     */
    public function setPrivateKey(CryptKey $privateKey) : void;

    /**
     * Generate a string representation of the access token.
     *
     * @return string
     */
    public function __toString();
}