<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Exception;

class UniqueTokenIdentifierConstraintViolationException extends OAuthException
{
    public static function create() : UniqueTokenIdentifierConstraintViolationException
    {
        $errorMessage = 'Could not create unique access token identifier';
        return new static($errorMessage, 100, 'access_token_duplicate', 500);
    }
}
