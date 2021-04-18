<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Request\Parame;

use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;
use Psr\Http\Message\ServerRequestInterface;

abstract class GrantTypeParame extends AbstractParame
{
    /**
     * The grant type
     * 
     * @var string
     */
    public const GRANT_TYPE = 'grant_type';

    /**
     * The client id.
     * 
     * @var string
     */
    public const CLIENT_ID = 'client_id';

    /**
     * The client secret.
     * 
     * @var string
     */
    public const CLIENT_SECRET = 'client_secret';

    /**
     * The grant type property
     * 
     * @var string
     */
    public string $grantType;

    /**
     * The client id.
     * 
     * @var string
     */
    public string $clientId;

    /**
     * The client secret.
     */
    public string $clientSecret;


    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function bindParame(ServerRequestInterface $request)
    {
        // set request parameter.
        $this->grantType = $this->getRequestParameter(self::GRANT_TYPE, $request);

        list($basicAuthUser, $basicAuthPassword) = $this->getBasicAuthCredentials($request);
        $this->clientId = $this->getRequestParameter(self::CLIENT_ID, $request, $basicAuthUser);
        if (is_null($this->clientId)) 
        {
            throw OAuthException::invalidRequest(self::CLIENT_ID);
        }
        $this->clientSecret = $this->getRequestParameter(self::CLIENT_SECRET, $request, $basicAuthPassword);
    }

    /**
     * Retrieve HTTP Basic Auth credentials with the Authorization header
     * of a request. First index of the returned array is the username,
     * second is the password (so list() will work). If the header does
     * not exist, or is otherwise an invalid HTTP Basic header, return
     * [null, null].
     *
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function getBasicAuthCredentials(ServerRequestInterface $request) : array
    {
        if (!$request->hasHeader('Authorization'))
        {
            return [null, null];
        }

        $header = $request->getHeader('Authorization')[0];
        if (strpos($header, 'Basic ') !== 0)
        {
            return [null, null];
        }

        if (!($decoded = base64_decode(substr($header, 6))))
        {
            return [null, null];
        }

        if (strpos($decoded, ':' === false))
        {
            return [null, null];
        }

        return explode(':', $decoded, 2);
    }
}

