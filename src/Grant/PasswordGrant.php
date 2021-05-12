<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Grant;

use BurningCloudSystem\OAuth2\Server\Crypt\CryptKey;
use BurningCloudSystem\OAuth2\Server\Exception\NotImplementedException;
use BurningCloudSystem\OAuth2\Server\Models\AccessTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\RefreshTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ScopeModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\UserModelInterface;
use BurningCloudSystem\OAuth2\Server\Request\Parame\PasswordGrantTypeParame;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use DateInterval;
use Defuse\Crypto\Key;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;

class PasswordGrant extends AbstractGrant implements GrantInterface
{
    /**
     * construct
     *
     * @param UserModelInterface $userModel
     * @param RefreshTokenModelInterface $refreshTokenModel
     */
    public function __construct(ClientModelInterface $clientModel,
                                ScopeModelInterface $scopeModel,
                                UserModelInterface $userModel)
    {
        parent::__construct($clientModel, $scopeModel);

        $this->setUserModel($userModel);
    }

    /**
     * Set token model.
     *
     * @param AccessTokenModelInterface $accessTokenModel
     * @param RefreshTokenModelInterface $refreshTokenModel
     * @param DateInterval|null $accessTokenTTL
     * @param DateInterval|null $refreshTokenTTL
     * @return void
     */
    public function setTokenModel(AccessTokenModelInterface $accessTokenModel, 
                                  RefreshTokenModelInterface $refreshTokenModel, 
                                  ?DateInterval $accessTokenTTL = null,
                                  ?DateInterval $refreshTokenTTL = null)
    {
        $this->setAccessTokenModel($accessTokenModel);
        $this->setRefreshTokenModel($refreshTokenModel);
        $this->setAccessTokenTTL($accessTokenTTL ?? new DateInterval('PT1H'));
        $this->setRefreshTokenTTL($refreshTokenTTL ?? new DateInterval('P1M'));
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'password';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getGrantType(): ?string
    {
        return 'password';
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
     * @return string
     */
    public function getResponseTypeParameClassName(): string
    {
        throw new LogicException('This grant cannot response type parame.');
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getGrantTypeParameClassName(): string
    {
        return PasswordGrantTypeParame::class;
    }

    /**
     * {@inheritDoc}
     *
     * @return PasswordGrantTypeParame
     */
    public function getGrantTypeParame() : PasswordGrantTypeParame
    {
        return parent::getGrantTypeParame();
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
     * @param ResponseTypeInterface $responseType
     * @return ResponseTypeInterface
     */
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseTypeInterface $responseType): ResponseTypeInterface
    {
        throw new NotImplementedException();
    }
}