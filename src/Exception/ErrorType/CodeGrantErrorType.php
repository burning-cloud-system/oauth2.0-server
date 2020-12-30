<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Exception\ErrorType;

/**
 * @link https://tex2e.github.io/rfc-translater/html/rfc6749.html
 * 
 * 4.1.2.1. Error Response
 */
class CodeGrantErrorType
{
    /**
     * The request is missing a required parameter, includes an unsupported parameter value,
     * includes a parameter more than once, or is otherwise malformed.
     * 
     * @var string
     */
    public const INVALID_REQUEST = 'invalid_request';

    /**
     * The client is not authorized to request an authorization code using this method
     * 
     * @var string
     */
    public const AUTHORIZED_CLIENT = 'authorized_client';

    /**
     * The resource owner or authorization server denied the request.
     * 
     * @var string
     */
    public const ACCESS_DENIED = 'access_denied';

    /**
     * The authorization server does not support obtaining an authorization code using this method.
     * 
     * @var string
     */
    public const UNSUPPORTED_RESPONSE_TYPE = 'unsupported_response_type';

    /**
     * The requested scope is invalid, unknown, or malformed.
     * 
     * @var string
     */
    public const INVALID_SCOPE = 'invalid_scope';

    /**
     * The authorization server encountered an unexpected condition that prevented it from fulfilling the request.
     * 
     * @var string
     */
    public const SERVER_ERROR = 'server_error';

    /**
     * The authorization server is currently unable to handle the request due to a temporary overloading or maintenance of the server.
     * 
     * @var string
     */
    public const constTEMPORARY_UNAVAILABLE = 'temporary_unavailable';

}