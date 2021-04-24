<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Request\Parame;

use BurningCloudSystem\OAuth2\Server\Request\Parame\RequestParameterTrait;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractParame
{
    use RequestParameterTrait;

    private const SCOPE_DELIMITER_STRING = ':';

    /**
     * Bind request parameter.
     *
     * @param object $parameter
     * @param ServerRequestInterface $request
     * @return void
     */
    abstract public function bindParame(ServerRequestInterface $request);

    /**
     * Converts a scopes query string to an array to easily iterate for validation.
     *
     * @param string $scopes
     * @return array
     */
    protected function converScopeStringToArray(string $scopes) : array
    {
        return array_filter(explode(self::SCOPE_DELIMITER_STRING, $scopes), function($scope){
            return !empty($scopes);
        });
    }
}