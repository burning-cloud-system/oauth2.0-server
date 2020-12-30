<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Entities;

use DateTimeImmutable;

interface AuthCodeEntityInterface
{
    /**
     * Get the authorization code identifier.
     *
     * @return string
     */
    public function getIdentifier() : string;

    /**
     * Set the authorization identifier
     *
     * @param string $identifier
     * @return void
     */
    public function setIdentifier(string $identifier) : void;

    /**
     * Returns the registered redirect URI.
     *
     * @return string|null
     */
    public function getRedirectUri() : ?string;

    /**
     * Set the registered redirect URI.
     *
     * @param string|null $uri
     * @return void
     */
    public function setRedirectUri(?string $uri) : void;

    /**
     * Get the authorization expiry date time.
     *
     * @return DateTimeImmutable
     */
    public function getExpiryDateTime() : DateTimeImmutable;

    /**
     * Set the date time when the authorization code expires.
     *
     * @param DateTimeImmutable $dateTime
     * @return void
     */
    public function setExpiryDateTime(DateTimeImmutable $dateTime) : void;

    /**
     * Set the identifier of the user associated with the authorization code.
     *
     * @param string $identifier
     * @return void
     */
    public function setUserIdentifier(string $identifier) : void;

    /**
     * Get the authorization code identifier
     *
     * @return string
     */
    public function getUserIdentifier() : string;

    /**
     * Get the client that the authorization code was issued to.
     *
     * @return ClientEntityInterface
     */
    public function getClient() : ClientEntityInterface;

    /**
     * Set the client that the authorization code was issued to.
     *
     * @param ClientEntityInterface $client
     * @return void
     */
    public function setClient(ClientEntityInterface $client) : void;

    /**
     * Associate a scope with the authorization code.
     *
     * @param ScopeEntityInterface $scope
     * @return void
     */
    public function addScope(ScopeEntityInterface $scope) : void;

    /**
     * Return an array of scopes associated with the authorization code.
     *
     * @return array
     */
    public function getScopes() : array;

}

