<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Event;

use League\Event\HasEventName;

class AbstractEvent implements HasEventName
{
    /**
     * @var string
     */
    private string $name;

    /**
     * Construct.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the event name.
     *
     * @return string
     */
    public function eventName(): string
    {
        return $this->name;
    }
}
