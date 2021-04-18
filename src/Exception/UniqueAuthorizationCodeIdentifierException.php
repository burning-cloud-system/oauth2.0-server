<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Exception;

use BurningCloudSystem\OAuth2\Server\Exception\ErrorType\AccessAuthorizationCodeDuplicate;
use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;

class UniqueAuthorizationCodeIdentifierException extends OAuthException
{
    /**
     * Construct.
     *
     * @param string $message
     */
    public function __construct(string $message = AccessAuthorizationCodeDuplicate::Message)
    {
        parent::__construct($message, 
                            AccessAuthorizationCodeDuplicate::Code, 
                            AccessAuthorizationCodeDuplicate::ErrorType,
                            AccessAuthorizationCodeDuplicate::HttpStatusCode);
    }
}
