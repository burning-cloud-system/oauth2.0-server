<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Models;

use BurningCloudSystem\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use BurningCloudSystem\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;

/**
 * Refresh token interface.
 */
interface RefreshTokenModelInterface extends ModelInterface
{
    /**
     * Creates a new refresh token
     *
     * @return RefreshTokenEntityInterface|null
     */
    public function getNewRefreshToken() : ?RefreshTokenEntityInterface;

    /**
     * Create a new refresh token name.
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     * @return void
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity) : void;

    /**
     * Revoke the refresh token.
     *
     * @param string $tokenId
     * @return void
     */
    public function revokeRefreshToken(string $tokenId) : void;

    /**
     * Check if the refresh token has been revoked.
     *
     * @param string $tokenId
     * @return boolean
     */
    public function isRefreshTokenRevoked(string $tokenId) : bool;
}
