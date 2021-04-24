<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Event\Listener;

use BurningCloudSystem\OAuth2\Server\Event\RequestEvent;

class SampleListener implements ClientAuthenticationFailedInterface
{
    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // handler event.
        
    }
}
