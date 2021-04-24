<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Exception\ErrorType;

class InvalidGrant
{
    /**
     * Error type
     * 
     * @var string
     */
    public const ErrorType = 'invalid_grant';

    /**
     * Error code
     * 
     * @var int
     */
    public const Code = 10;

    /**
     * Error mmessage
     * 
     * @var string
     */
    public const Message = 'The provided authorization grant (e.g., authorization code, resource owner credentials) or refresh token ' . 
                            'is invalid, expired, revoked, does not match the redirection URI used in the authorization request, ' .
                            'or was issued to another client.';

    /**
     * Error http status code
     * 
     * @var int
     */
    public const HttpStatusCode = 400;
}