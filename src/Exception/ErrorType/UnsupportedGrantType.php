<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Exception\ErrorType;

class UnsupportedGrantType
{
    /**
     * Error type
     * 
     * @var string
     */
    public const ErrorType = 'unsupported_grant_type';

    /**
     * Error code
     * 
     * @var int
     */
    public const Code = 2; 

    /**
     * Error message
     * 
     * @var string
     */
    public const Message = 'The authorization grant type is not supported by the authorization server.';

    /**
     * Error http status code
     * 
     * @var int
     */
    public const HttpStatusCode = 400;
}