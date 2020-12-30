<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

 namespace BurningCloudSystem\OAuth2\Server\Request\Parame;

use Psr\Http\Message\ServerRequestInterface;

trait RequestParameterTrait
 {
    /**
     * Retrieve query string parameter.
     *
     * @param string                 $parameter
     * @param ServerRequestInterface $request
     * @param mixed                  $default
     *
     * @return null|string
     */
    protected function getQueryStringParameter($parameter, ServerRequestInterface $request, $default = null, $trim = true)
    {
        return $this->getValueToParams($request->getQueryParams(), $parameter, $default);
    }

    /**
     * Retrieve server parameter.
     *
     * @param string                 $parameter
     * @param ServerRequestInterface $request
     * @param mixed                  $default
     *
     * @return null|string
     */
    protected function getServerParameter($parameter, ServerRequestInterface $request, $default = null)
    {
        return $this->getValueToParams($request->getServerParams(), $parameter, $default);
    }

    private function getValueToParams(&$params, &$key, $default, $trim = false) : string
    {
        return isset($params[$key]) ? $this->getValue($params[$key], $trim) : $default;
    }

    private function getValue($value, $trim = false) : string
    {
        $trim && ($value = trim($value));
        return strlen($value) > 0 ? $value : null;
    }
 }

 