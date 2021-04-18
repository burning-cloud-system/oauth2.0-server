<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2010 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Grant;

use BurningCloudSystem\OAuth2\Server\Crypt\CryptKey;
use BurningCloudSystem\OAuth2\Server\Exception\NotImplementedException;
use BurningCloudSystem\OAuth2\Server\Models\AccessTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ScopeModelInterface;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Defuse\Crypto\Key;
use Psr\Http\Message\ServerRequestInterface;

class RefreshTokenGrant extends AbstractGrant implements GrantInterface
{
    /**
     * construct.
     *
     * @param string $privateKey
     * @param string $encryptionKey
     * @param ClientModelInterface $clientModel
     * @param ScopeModelInterface $scopeModel
     * @param AccessTokenModelInterface $accessTokenModel
     */
    public function __construct(string $privateKey,
                                string $encryptionKey,
                                ClientModelInterface $clientModel,
                                ScopeModelInterface $scopeModel,
                                AccessTokenModelInterface $accessTokenModel)
    {
        
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'refresh_token';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getGrantType(): ?string
    {
        return 'refresh_token';
    }

    /**
     * {@inheritDoc}
     *
     * @return string|null
     */
    public function getResponseType(): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @param ClientModelInterface $clientModel
     * @return void
     */
    public function setClientModel(ClientModelInterface $clientModel): void
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @param string $defaultScope
     * @return void
     */
    public function setDefaultScope(string $defaultScope): void
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @param CryptKey $privateKey
     * @return void
     */
    public function setPrivateKey(CryptKey $privateKey): void
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @param Key|null $key
     * @return void
     */
    public function setEncryptionKey(?Key $key = null): void
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return boolean
     */
    public function canRespondToAuthorizationRequest(ServerRequestInterface $request): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return void
     * @throws OAuthException
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request): void
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @return ResponseTypeInterface
     * @throws OAuthException
     */
    public function completeAuthorizationRequest(): ResponseTypeInterface
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return boolean
     * @throws OAuthException
     */
    public function canRespondToAccessTokenRequest(ServerRequestInterface $request): bool
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return void
     * @throws OAuthException
     */
    public function validateAccessTokenRequest(ServerRequestInterface $request): void
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @return ResponseTypeInterface
     * @throws OAuthException
     */
    public function respondToAccessTokenRequest(): ResponseTypeInterface
    {
        throw new NotImplementedException();
    }
}