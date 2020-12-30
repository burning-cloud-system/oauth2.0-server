<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Grant;

trait AuthorizeGrantTrait
{
    /**
     * Make redirect uri
     *
     * @param string $uri
     * @param array $params
     * @param string $queryDelimiter
     * @return string
     */
    public function makeRedirectUri(string $uri, array $params = [], string $queryDelimiter = '?') : string
    {
        $uri .= (strstr($uri, $queryDelimiter) === false) ? $queryDelimiter : '&';
        return $uri . http_build_query($params);
    }
}