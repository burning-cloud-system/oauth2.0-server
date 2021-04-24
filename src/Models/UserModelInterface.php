<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2010 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Models;

use BurningCloudSystem\OAuth2\Server\Entities\ClientEntityInterface;

interface UserModelInterface extends ModelInterface
{
    /**
     * Get a user entity.
     *
     * @param string $username
     * @param string $password
     * @param ClientEntityInterface $clientEntity
     * @return void
     */
    public function getUserEntityByUserCredentials(
        string $username,string $password, ClientEntityInterface $clientEntity);
}
