<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Models;

use BurningCloudSystem\OAuth2\Server\Entities\AccessTokenEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\ClientEntityInterface;

interface AccessTokenModelInterface extends ModelInterface
{
    /**
     * Create a new access token.
     *
     * @param ClientEntityInterface $clientModel
     * @param array $scopes
     * @param string|null $userIdentifier
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientModel, array $scopes, ?string $userIdentifier = null) : AccessTokenEntityInterface;

    /**
     * Persists a new access token to permanent storage.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     * @return void
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void;
    
    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     * @return void
     */
    public function revokeAccessToken(string $tokenId) : void;

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     * @return boolean
     */
    public function isAccessTokenRevoked(string $tokenId) : bool;
}

