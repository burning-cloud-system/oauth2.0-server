<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Entities;

interface ClientEntityInterface
{
    /**
     * Get the client's identifier
     *
     * @return string
     */
    public function getIdentfier() : string;

    /**
     * Get the client's name.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Returns the registered redirect URI.
     *
     * @return string|null
     */
    public function getRedirectUri() : ?string;

    /**
     * Returns true if the client is confidential.
     *
     * @return boolean
     */
    public function isConfidential() : bool;
}

