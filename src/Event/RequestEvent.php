<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Event;

use Psr\Http\Message\ServerRequestInterface;

class RequestEvent extends AbstractEvent
{
    // public const CLIENT_AUTHENTICATION_FAILED = 'client.authentication.failed';
    public const USER_AUTHENTICATION_FAILED = 'user.authentication.failed';
    public const REFRESH_TOKEN_CLIENT_FAILED = 'refresh.token.client.failed';

    public const REFRESH_TOKEN_ISSUED = 'refresh.token.issued';
    // public const ACCESS_TOKEN_ISSUED = 'access.token.issued';

    /**
     * @var ServerRequestInterface
     */
    private ServerRequestInterface $request;

    /**
     * Construct.
     *
     * @param string $name
     */
    public function __construct(string $name, ServerRequestInterface $request)
    {
        parent::__construct($name);
        $this->request = $request;
    }

    /**
     * Get server request.
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

}
