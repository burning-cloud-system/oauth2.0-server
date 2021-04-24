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

class PasswordGrantTypeParame extends GrantTypeParame
{
    /**
     * The username.
     * 
     * @var string
     */
    public const USERNAME = 'username';

    /**
     * The password.
     * 
     * @var string
     */
    public const PASSWORD = 'password';

    /**
     * The scope
     * 
     * @var string
     */
    public const SCOPE = 'scope';

    /**
     * The username property
     * 
     * @var string
     */
    public string $username;

    /**
     * The password property
     * 
     * @var string
     */
    public string $password;

    /**
     * The scope property
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

        $this->username = $this->getQueryStringParameter(self::USERNAME, $request);
        $this->password = $this->getQueryStringParameter(self::PASSWORD, $request);
        $this->scope    = $this->getQueryStringParameter(self::SCOPE,    $request);

        $this->scopes = $this->converScopeStringToArray($this->scope);
    }
} 

