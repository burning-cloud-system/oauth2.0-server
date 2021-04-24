<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Exception\ErrorType;

class InvalidScope
{
    /**
     * Error type
     * 
     * @var string
     */
    public const ErrorType = 'invalid_scope';

    /**
     * Error code
     * 
     * @var int
     */
    public const Code = 4; 

    /**
     * Error message
     * 
     * @var string
     */
    public const Message = 'The requested scope is invalid, unknown, or malformed.';

    /**
     * Error http status code
     * 
     * @var int
     */
    public const HttpStatusCode = 400;
}