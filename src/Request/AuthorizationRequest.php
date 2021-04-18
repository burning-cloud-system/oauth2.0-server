<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Request;

use BurningCloudSystem\OAuth2\Server\Entities\ClientEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\ScopeEntityInterface;
use BurningCloudSystem\OAuth2\Server\Entities\UserEntityInterface;

trait AuthorizationRequest
{
    /**
     * The grant type identifier
     * 
     * @var string
     */
    protected string $grantTypeId;

    /**
     * The client identifier
     * 
     * @var ClientEntityInterface
     */
    protected ClientEntityInterface $client;

    /**
     * The user identifier
     * 
     * @var UserEntityInterface
     */
    protected UserEntityInterface $user;

    /**
     * An array of scope identifiers
     *
     * @var ScopeEntityInterface[]
     */
    protected $scopes = [];

    /**
     * Has the user authorizaed the authorization request
     * 
     * @var bool
     */
    protected bool $authorizationApproved = false;

    /**
     * The redirect URI used in the request
     * 
     * @var string|null
     */
    protected ?string $redirectUri;

    /**
     * The state parameter on the authorization request
     * 
     * @var string|null
     */
    protected ?string $state;

    /**
     * The code callenge
     * 
     * @var string
     */
    protected ?string $codeChallenge;

    /**
     * The code callenge method
     * 
     * @var string
     */
    protected string $codeChallengeMethod;

    /**
     * Return the grant type identifier.
     *
     * @return string
     */
    public function getGrantTypeId() : string
    {
        return $this->grantTypeId;
    }

    /**
     * Set the grant type identifier.
     *
     * @param string $grantTypeId
     * @return void
     */
    public function setGrantTypeId(string $grantTypeId) : void
    {
        $this->grantTypeId = $grantTypeId;
    }

    /**
     * Return the client identifier
     *
     * @return ClientEntityInterface
     */
    public function getClient() : ClientEntityInterface
    {
        return $this->client;
    }

    /**
     * Set the client identifier
     *
     * @param ClientEntityInterface $client
     * @return void
     */
    public function setClient(ClientEntityInterface $client) : void
    {
        $this->client = $client;
    }

    /**
     * Return the user.
     *
     * @return UserEntityInterface
     */
    public function getUser() : UserEntityInterface
    {
        return $this->user;        
    }

    /**
     * Set the user.
     *
     * @param UserEntityInterface $user
     * @return void
     */
    public function setUser(UserEntityInterface $user) : void
    {
        $this->user = $user;
    }

    /**
     * Return the scopes.
     *
     * @return array
     */
    public function getScopes() : array
    {
        return $this->scopes;
    }

    /**
     * Set the scopes.
     *
     * @param array $scopes
     * @return void
     */
    public function setScopes(array $scopes) : void
    {
        $this->scopes = $scopes;
    }

    /**
     * Return the authorization approved.
     *
     * @return boolean
     */
    public function isAuthorizationApproved() : bool
    {
        return $this->authorizationApproved;
    }

    /**
     * Set the authorization approved.
     *
     * @param boolean $authorizationApproved
     * @return void
     */
    public function setAuthorizationApproved(bool $authorizationApproved) : void
    {
        $this->authorizationApproved = $authorizationApproved;
    }

    /**
     * Return the redirect uri.
     *
     * @return string
     */
    public function getRedirectUri() : string
    {
        return $this->redirectUri;
    }

    /**
     * Set the redirect uri.
     *
     * @param string|null $redirectUri
     * @return void
     */
    public function setRedirectUri(?string $redirectUri) : void
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * Return the state.
     *
     * @return string|null
     */
    public function getState() : ?string
    {
        return $this->state;
    }

    /**
     * Set the state.
     *
     * @param string|null $state
     * @return void
     */
    public function setState(?string $state) : void
    {
        $this->state = $state;
    }

    /**
     * Return the code challenge
     *
     * @return string|null
     */
    public function getCodeChallenge() : ?string
    {
        return $this->codeChallenge;
    }

    /**
     * Set the code challenge
     *
     * @param string|null $codeChallenge
     * @return void
     */
    public function setCodeChallenge(?string $codeChallenge) : void
    {
        $this->codeChallenge = $codeChallenge;
    }

    /**
     * Return the code callenge method.
     *
     * @return string
     */
    public function getCodeChallengeMethod() : string
    {
        return $this->codeChallengeMethod;
    }

    /**
     * Set the code challenge method.
     *
     * @param string $codeChallengeMethod
     * @return void
     */
    public function setCodeChallengMethod(string $codeChallengeMethod) : void
    {
        $this->codeChallengeMethod = $codeChallengeMethod;
    }
}
