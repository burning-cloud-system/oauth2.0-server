<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Exception\ErrorType;

class AccessAuthCodeDuplicate
{
    /**
     * Error type
     * 
     * @var string
     */
    public const ErrorType = 'access_authorization_code_duplicate';

    /**
     * Error code
     * 
     * @var int
     */
    public const Code = 99; 

    /**
     * Error message
     * 
     * @var string
     */
    public const Message = 'Could not create unique access authorization code identifier';

    /**
     * Error http status code
     * 
     * @var int
     */
    public const HttpStatusCode = 500;
}