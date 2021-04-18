<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\ResponseTypes;

use BurningCloudSystem\OAuth2\Server\Entities\AccessTokenEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use Defuse\Crypto\Key;
use Psr\Http\Message\ResponseInterface;

interface ResponseTypeInterface
{
    /**
     * @param AccessTokenEntityInterface $accessToken
     * @return void
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken) : void;

    /**
     * @param RefreshTokenEntityInterface $refreshToken
     * @return void
     */
    public function setRefreshToken(RefreshTokenEntityInterface $refreshToken) : void;

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function generateHttpResponse(ResponseInterface $response) : ResponseInterface;

    /**
     * Set the encryption key
     *
     * @param string|Key|null $key
     * @return void
     */
    public function setEncryptionKey($key = null) : void;
}
