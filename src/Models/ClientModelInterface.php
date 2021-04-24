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

interface ClientModelInterface extends ModelInterface
{
    /**
     * Get a client.
     *
     * @param string $clientIdentifier
     * @return ClientEntityInterface
     */
    public function getClientEntity(string $clientIdentifier): ClientEntityInterface;

    /**
     * Validate a client's secret.
     *
     * @param string $clientIdentifier
     * @param string|null $clientSecret
     * @param string|null $grantType
     * @return boolean
     */
    public function validateClient(string $clientIdentifier, ?string $clientSecret, ?string $grantType) : bool;
}

