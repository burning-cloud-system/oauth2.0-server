<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Event\Listener;

use League\Event\Listener;

interface AccessTokenIssuedInterface extends Listener
{
    public const EVENT_NAME = "access.token.issued";
}
