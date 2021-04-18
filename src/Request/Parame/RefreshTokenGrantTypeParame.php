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

class RefreshTokenGrantTypeParame extends ResponseTypeParame
{
    /**
     * The refresh token
     * 
     * @var string
     */
    public const REFRESH_TOKEN = 'refresh_token';

    /**
     * The scope.
     */
    public const SCOPE = 'scope';

    /**
     * The refresh token property used in the request
     * 
     * @var string
     */
    public string $refreshToken;

    /**
     * The scope property used in the request
     * 
     * @var string
     */
    public string $scope;


    /**
     * The scope property used in the request 
     *
     * @var string[]
     */
    public array $scopes = [];

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function bindParame(ServerRequestInterface $request)
    {
        // set request parameter.
        parent::bindParame($request);

        $this->refreshToken = $this->getQueryStringParameter(self::REFRESH_TOKEN, $request);
        $this->scope    = $this->getQueryStringParameter(self::SCOPE, $request);

        $this->scopes = $this->converScopeStringToArray($this->scope);
    }

}
