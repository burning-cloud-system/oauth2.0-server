<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Entities;

use DateTimeImmutable;

interface RefreshTokenEntityInterface
{

    /**
     * Get the token's identifier.
     *
     * @return string
     */
    public function getIdentifier() : string;

    /**
     * Set the token's identifier.
     *
     * @param string $identifier
     * @return void
     */
    public function setIdentifier(string $identifier) : void;

    /**
     * Get the token's expiry date time.
     *
     * @return DateTimeImmutable
     */
    public function getExpiryDateTime() : DateTimeImmutable;

    /**
     * Set the date time when the token expires.
     *
     * @param DateTimeImmutable $dateTime
     * @return void
     */
    public function setExpiryDateTime(DateTimeImmutable $dateTime) : void;

    /**
     * Set the access token that the refresh token was associated with.
     *
     * @param AccessTokenEntityInterface $accessToken
     * @return void
     */
    public function setAccessToken(AccessTokenEntityInterface $accessToken) : void;

    /**
     * Get the access token that the refresh token was originally associated with.
     *
     * @return AccessTokenEntityInterface
     */
    public function getAccessToken() : AccessTokenEntityInterface;
}

