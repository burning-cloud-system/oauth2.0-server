<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Exception\ErrorType;

class InvalidRequest
{
    /**
     * Error type
     * 
     * @var string
     */
    public const ErrorType = 'invalid_request';

    /**
     * Error code
     * 
     * @var int
     */
    public const Code = 3; 

    /**
     * Error message
     * 
     * @var string
     */
    public const Message = 'The request is missing a required parameter, includes an unsupported parameter value, ' . 
                           'includes a parameter more than once, or is otherwise malformed.';

    /**
     * Error http status code
     * 
     * @var int
     */
    public const HttpStatusCode = 400;
}