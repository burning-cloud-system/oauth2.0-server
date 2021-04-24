<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Exception\ErrorType;

class ServerError
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
    public const Code = 7; 

    /**
     * Error message
     * 
     * @var string
     */
    public const Message = 'The authorization server encountered an unexpected condition that prevented it from fulfilling the request.';
    
    /**
     * Error http status code
     * 
     * @var int
     */
    public const HttpStatusCode = 500;
}