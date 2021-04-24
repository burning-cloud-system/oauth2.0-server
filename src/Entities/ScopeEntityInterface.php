<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Entities;

interface ScopeEntityInterface
{
    /**
     * Get the scope's identifier.
     *
     * @return string
     */
    public function getIdentifier() : string;
}

