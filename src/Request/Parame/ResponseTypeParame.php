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
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

abstract class ResponseTypeParame extends AbstractParame
{

	/**
	 * The response type
	 * 
	 * @var string
	 */
    public const RESPONSE_TYPE = 'response_type';

    /**
     * The client identifie
     *
     * @var string
     */
    public const CLIENT_ID = "client_id";

    /**
     * The redirect URI used in the request
     * 
     * @var string
     */
    public const REDIRECT_URI = "redirect_uri";

    /**
     * The scope identifiers
     * 
     * @var string
     */
    public const SCOPE = "scope";

    /**
     * The state parameter on the authorization request
     *
     * @var string
     */
    public const STATE = "state";

    /**
     * The response type property
     * 
     * @var string
     */
    public string $responseType;

    /**
     * The client identifier property
     *
     * @var string
     */
    public string $clientId;

    /**
     * The redirect URI property used in the request 
     *
     * @var string
     */
    public string $redirectUri;

    /**
     * The scope property used in the request
     */
    public string $scope;

    /**
     * The scope property used in the request 
     *
     * @var string[]
     */
    public array $scopes = [];

    /**
     * The state property on the authorization request
     *
     * @var string|null
     */
    public ?string $state;

	/**
	 * {@inheritDoc}
	 *
	 * @param ServerRequestInterface $request
	 * @return void
	 */
    public function bindParame(ServerRequestInterface $request)
    {
        // set request parameter.
        try {
            $this->responseType  = $this->getQueryStringParameter(self::RESPONSE_TYPE,  $request);
        } catch (Throwable $e) {
            throw OAuthException::invalidRequest(self::RESPONSE_TYPE, null, $e);
        }

        try {
            $this->clientId      = $this->getQueryStringParameter(self::CLIENT_ID,      $request);
        } catch (Throwable $e) {
            // throw OAuthException::invalidRequest(self::CLIENT_ID, null, $e);
        }

        if (!isset($this->clientId))
        {
            try {
                $this->clientId = $this->getServerParameter('PHP_AUTH_USER', $request);
            } catch (Throwable $e) {
                throw OAuthException::invalidRequest(self::CLIENT_ID, null, $e);
            }
   
        }

        try {
            $this->redirectUri   = $this->getQueryStringParameter(self::REDIRECT_URI,   $request);
        } catch (Throwable $e) {
            throw OAuthException::invalidRequest(self::REDIRECT_URI, null, $e);
        }

        try {
            $this->scope         = $this->getQueryStringParameter(self::SCOPE,          $request);
        } catch (Throwable $e) {
            throw OAuthException::invalidRequest(self::SCOPE, null, $e);
        }

        try {
            $this->state         = $this->getQueryStringParameter(self::STATE,          $request);
        } catch (Throwable $e) {
            throw OAuthException::invalidRequest(self::STATE, null, $e);
        }

        $this->scopes        = $this->converScopeStringToArray($this->scope);
    }
}
