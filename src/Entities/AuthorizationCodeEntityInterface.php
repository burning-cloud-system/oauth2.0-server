<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Entities;

interface AuthorizationCodeEntityInterface extends TokenInterface
{
    /**
     * Returns the registered redirect URI.
     *
     * @return string|null
     */
    public function getRedirectUri() : ?string;

    /**
     * Set the registered redirect URI.
     *
     * @param string|null $uri
     * @return void
     */
    public function setRedirectUri(?string $uri) : void;
}

