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
      * @param string $parameter
      * @param ServerRequestInterface $request
      * @param [type] $default
      * @param boolean $trim
      * @return void
      */
     protected function getRequestParameter(string $parameter, ServerRequestInterface $request, $default = null, $trim = true)
     {
         return $this->getValueToParams($request->getParsedBody(), $parameter, $default);
     }

    /**
     * Retrieve query string parameter.
     *
     * @param string                 $parameter
     * @param ServerRequestInterface $request
     * @param mixed                  $default
     *
     * @return null|string
     */
    protected function getQueryStringParameter(string $parameter, ServerRequestInterface $request, $default = null, $trim = true)
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
    protected function getServerParameter(string $parameter, ServerRequestInterface $request, $default = null)
    {
        return $this->getValueToParams($request->getServerParams(), $parameter, $default);
    }

    private function getValueToParams($params, $key, $default, $trim = false)
    {
        if (array_key_exists($key, $params))
        {
            return $this->getValue($params[$key], $trim);
        } else
        {
            return $default;
        }
    }

    private function getValue($value, $trim = false)
    {
        $trim && ($value = trim($value));
        if (strlen($value))
        {
            return $value;
        }
        else
        {
            return "";
        }
    }
 }

 