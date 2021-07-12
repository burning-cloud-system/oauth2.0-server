<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2010 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Grant;

use BurningCloudSystem\OAuth2\Server\Event\Listener\AccessTokenIssuedInterface;
use BurningCloudSystem\OAuth2\Server\Event\Listener\ClientAuthenticationFailedInterface;
use BurningCloudSystem\OAuth2\Server\Event\RequestEvent;
use BurningCloudSystem\OAuth2\Server\Exception\OAuthException;
use BurningCloudSystem\OAuth2\Server\Models\AccessTokenModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ClientModelInterface;
use BurningCloudSystem\OAuth2\Server\Models\ScopeModelInterface;
use BurningCloudSystem\OAuth2\Server\Request\Parame\ClientCredentialsGrantTypeParame;
use BurningCloudSystem\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use DateInterval;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;

class ClientCredentialsGrant extends AbstractGrant implements GrantInterface
{
    public function __construct(ClientModelInterface $clientModel,
                                ScopeModelInterface $scopeModel)
    {
        parent::__construct($clientModel, $scopeModel);
    }

    /**
     * Set token model.
     *
     * @param AccessTokenModelInterface $accessTokenModel
     * @param DateInterval|null $accessTokenTTL
     * @return void
     */
    public function setTokenModel(AccessTokenModelInterface $accessTokenModel, 
                                  ?DateInterval $accessTokenTTL = null) : void
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
        return 'client_credentials';
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
    public function getGrantTypeParameClassName(): string
    {
        return ClientCredentialsGrantTypeParame::class;
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
     * @return ClientCredentialsGrantTypeParame
     */
    public function getGrantTypeParame() : ClientCredentialsGrantTypeParame
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
     * @return ResponseTypeInterface
     * @throws OAuthException
     */
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseTypeInterface $responseType): ResponseTypeInterface
    {
        $client = $this->getClientEntity($this->getGrantTypeParame()->clientId, $request);

        if (!$client->isConfidential())
        {
            $this->eventDispatcher()->dispatch(new RequestEvent(ClientAuthenticationFailedInterface::EVENT_NAME, $request));
            throw OAuthException::invalidClient($request);
        }

        $this->validateClient($request);

        $scopes = $this->validateScopes($this->getGrantTypeParame()->scopes);

        $finalizedScopes = $this->scopeModel->finalizeScopes($scopes, $this->getGrantType(), $client);

        $accessToken = $this->issueAccessToken($this->accessTokenTTL, $client, null, $finalizedScopes);

        $this->eventDispatcher()->dispatch(new RequestEvent(AccessTokenIssuedInterface::EVENT_NAME, $request));

        $responseType->setAccessToken($accessToken);

        return $responseType;
    }
}
