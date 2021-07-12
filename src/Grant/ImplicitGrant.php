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
use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;
use BurningCloudSystem\OAuth2\Server\Models\AccessTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ScopeModelInterface;
use BurningCloudSystem\OAuth2\Server\Request\AuthorizationRequest;
use BurningCloudSystem\OAuth2\Server\Request\Parame\ImplicitResponseTypeParame;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use DateInterval;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;

class ImplicitGrant extends AbstractAuthorizationCodeGrant implements GrantInterface 
{
    /**
     * @var string
     */
    private string $queryDelimiter;

    /**
     * construct
     *
     * @param ClientModelInterface $clientModel
     * @param ScopeModelInterface $scopeModel
     * @param string $queryDelimiter
     */
    public function __construct(ClientModelInterface $clientModel,
                                ScopeModelInterface $scopeModel,
                                string $queryDelimiter = '*')
    {
        parent::__construct($clientModel, $scopeModel);

        $this->queryDelimiter = $queryDelimiter;
    }

    /**
     * Set token model.
     *
     * @param AccessTokenModelInterface $accessTokenModel
     * @param DateInterval|null $accessTokenTTL
     * @return void
     */
    public function setTokenModel(AccessTokenModelInterface $accessTokenModel, 
                                  ?DateInterval $accessTokenTTL = null)
    {
        $this->setAccessTokenModel($accessTokenModel);
        $this->setAccessTokenTTL($accessTokenTTL ?? new DateInterval('PT1H'));
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getGrantType(): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @return string|null
     */
    public function getResponseType(): ?string
    {
        return 'token';
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getResponseTypeParameClassName(): string
    {
        return ImplicitResponseTypeParame::class;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getGrantTypeParameClassName(): string
    {
        throw new LogicException('This grant cannot grant type parame.');
    }

    /**
     * {@inheritDoc}
     *
     * @return ImplicitResponseTypeParame
     */
    public function getResponseTypeParame() : ImplicitResponseTypeParame
    {
        return parent::getResponseTypeParame();
    }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return AuthorizationRequest
     * @throws OAuthException
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request): AuthorizationRequest
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @return ResponseTypeInterface
     * @throws OAuthException
     */
    public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest): ResponseTypeInterface
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return boolean
     */
    public function canRespondToAccessTokenRequest(ServerRequestInterface $request): bool
    {
        return false;
    }
}

