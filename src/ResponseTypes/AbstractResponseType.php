<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\ResponseTypes;

use BurningCloudSystem\OAuth2\Server\Crypt\CryptKey;
use BurningCloudSystem\OAuth2\Server\Crypt\CryptTrait;
use BurningCloudSystem\OAuth2\Server\Entities\AccessTokenEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\RefreshTokenEntityInterface;

abstract class AbstractResponseType implements ResponseTypeInterface
{
    use CryptTrait;

    /**
     * @var AccessTokenEntityInterface
     *
     */
    protected AccessTokenEntityInterface $accessToken;

    /**
     * @var RefreshTokenEntityInterface
     */
    protected RefreshTokenEntityInterface $refreshToken;

    /**
     * @var CryptKey
     */
    protected CryptKey $privateKey;

    /**
     * {@inheritdoc}
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken) : void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setRefreshToken(RefreshTokenEntityInterface $refreshToken) : void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * Set the private key
     *
     * @param CryptKey $key
     */
    public function setPrivateKey(CryptKey $key)
    {
        $this->privateKey = $key;
    }
}
