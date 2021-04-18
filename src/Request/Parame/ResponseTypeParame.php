<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Request\Parame;

use Psr\Http\Message\ServerRequestInterface;

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
        $this->responseType  = $this->getQueryStringParameter(self::RESPONSE_TYPE,  $request);
        $this->clientId      = $this->getQueryStringParameter(self::CLIENT_ID,      $request);
        $this->redirectUri   = $this->getQueryStringParameter(self::REDIRECT_URI,   $request);
        $this->scope         = $this->getQueryStringParameter(self::SCOPE,          $request);
        $this->state         = $this->getQueryStringParameter(self::STATE,          $request);

        $this->scopes        = $this->converScopeStringToArray($this->scope);

        if ($this->clientId === null) 
        {
            $this->clientId = $this->getServerParameter('PHP_AUTH_USER', $request);
        }
    }
}
