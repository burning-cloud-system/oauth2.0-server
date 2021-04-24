<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Models;

use BurningCloudSystem\OAuth2\Server\Entities\ClientEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\ScopeEntityInterface;

interface ScopeModelInterface
{
    /**
     * Return information about a scope.
     *
     * @param string $identifier
     * @return ScopeEntityInterface|null
     */
    public function getScopeEntity(string $identifier) : ?ScopeEntityInterface;

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param array $scopes
     * @param string $grantType
     * @param ClientEntityInterface $client
     * @param string|null $userIdentifier
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(array $scopes, string $grantType, ClientEntityInterface $client, ?string $userIdentifier = null) : array;
}

