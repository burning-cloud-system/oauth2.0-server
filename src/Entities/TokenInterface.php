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

interface TokenInterface
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
     * Set the identifier of the user associated with the token.
     *
     * @param string|null $identifier
     * @return void
     */
    public function setUserIdentifier(?string $identifier) : void;

    /**
     * Get the token user's identifier.
     *
     * @return string|null
     */
    public function getUserIdentifier() : ?string;

    /**
     * Get the client that the token was issued to.
     *
     * @return ClientEntityInterface
     */
    public function getClient() : ClientEntityInterface;

    /**
     * Set the client that the token was issued to.
     *
     * @param ClientEntityInterface $client
     * @return void
     */
    public function setClient(ClientEntityInterface $client) : void;

    /**
     * Associate a scope with the token.
     *
     * @param ScopeEntityInterface $scope
     * @return void
     */
    public function addScope(ScopeEntityInterface $scope) : void;

    /**
     * Return an array of scopes associated with the token.
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes();
}
