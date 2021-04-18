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
use LogicException;
use Psr\Http\Message\ResponseInterface;

class BearerTokenResponse extends AbstractResponseType
{
    /**
     * {@inheritDoc}
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function generateHttpResponse(ResponseInterface $response) : ResponseInterface
    {
        $expireDateTime = $this->accessToken->getExpiryDateTime()->getTimestamp();

        $responseParams = [
            'token_type'    => 'Bearer',
            'expires_in'    => $expireDateTime - time(),
            'access_token'  => (string) $this->accessToken,
        ];

        if ($this->refreshToken instanceof RefreshTokenEntityInterface)
        {
            $refreshTokenPayload = json_encode([
                'client_id'         => $this->accessToken->getClient()->getIdentifier(),
                'refresh_token_id'  => $this->refreshToken->getIdentifier(),
                'access_token_id'   => $this->accessToken->getIdentifier(),
                'scopes'            => $this->accessToken->getScopes(),
                'user_id'           => $this->accessToken->getUserIdentifier(),
                'expire_time'       => $this->refreshToken->getExpiryDateTime()->getTimestamp(),
            ]);

            if ($refreshTokenPayload === false)
            {
                throw new LogicException('Error encountered JSON encoding the refresh token payload');
            }

            $responseParams['refresh_token'] = $this->encrypt($refreshTokenPayload);
        }

        $responseParams = json_encode(array_merge($this->getExtraParams($this->accessToken), $responseParams));

        if ($responseParams === false)
        {
            throw new LogicException('Error encountered JSON encoding response parameters');
        }

        $response = $response
                        ->withStatus(200)
                        ->withHeader('pragma', 'no-cache')
                        ->withHeader('cache-control', 'no-store')
                        ->withHeader('content-type', 'application/json; charset=UTF-8');

        $response->getBody()->write($responseParams);

        return $response;
    }

    /**
     * Add custom fields to your Bearer Token response here, then override
     * AuthorizationServer::getResponseType() to pull in your version of
     * this class rather than the default.
     *
     * @param AccessTokenEntityInterface $accessToken
     * @return array
     */
    protected function getExtraParams(AccessTokenEntityInterface $accessToken) : array
    {
        return [];
    }
}

