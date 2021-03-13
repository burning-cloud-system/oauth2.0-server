<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Request\Parame;

use Psr\Http\Message\ServerRequestInterface;

class AuthorizationParame extends AbstractParame
{
    /**
      * The response type
      * 
      * @var string
      */
    public const RESPONSE_TYPE = 'response_type';

    /**
      * The client identifie
      *
      * @var string
      */
    public const CLIENT_ID = "client_id";

    /**
      * The redirect URI used in the request
      * 
      * @var string
      */
    public const REDIRECT_URI = "redirect_uri";

    /**
      * The scope identifiers
      * 
      * @var string
      */
    public const SCOPE = "scope";

    /**
      * The state parameter on the authorization request
      *
      * @var string
      */
    public const STATE = "state";

    /**
      * tHE CODE CHALLENGE 
      * @var string
      */
    public const CODE_CHALLENGE = "code_challenge";

    /**
      * The code challenge method
      *
      * @var string
      */
    public const CODE_CHALLENGE_METHOD = "code_challenge_method";

    /**
      * The response type property
      * 
      * @var string
      */
    public string $responseType;

    /**
      * The client identifier property
      *
      * @var string
      */
    public string $clientId;

    /**
      * The redirect URI property used in the request 
      *
      * @var string
      */
    public string $redirectUri;

    /**
      * The scope property used in the request 
      *
      * @var string[]
      */
    public array $scopes = [];

    /**
      * The state property on the authorization request
      *
      * @var string|null
      */
    public ?string $state;

    /**
      * The code challenge property used in the request 
      *
      * @var  string|null
      */
    public ?string $codeChallenge;
     
    /**
      * The code challenge method property used in the request 
      *
      * @var string
      */
    public string $codeChallengeMethod;

    /**
     * Bind request parameter.
     *
     * @param object $parameter
     * @param ServerRequestInterface $request
     * @return void
     */
    public function bindParame(ServerRequestInterface $request)
    {
        // set request parameter.
        $this->responseType        = $this->getQueryStringParameter(self::RESPONSE_TYPE,  $request);
        $this->clientId            = $this->getQueryStringParameter(self::CLIENT_ID,      $request);
        $this->redirectUri         = $this->getQueryStringParameter(self::REDIRECT_URI,   $request);
        $this->scopes              = $this->converScopeStringToArray($this->getQueryStringParameter(self::SCOPE, $request));
        $this->state               = $this->getQueryStringParameter(self::STATE,          $request);
        $this->codeChallenge       = $this->getQueryStringParameter(self::CODE_CHALLENGE, $request);
        $this->codeChallengeMethod = $this->getQueryStringParameter(self::CODE_CHALLENGE_METHOD, $request, 'plain');

        if ($this->clientId === null) 
        {
            $this->clientId = $this->getServerParameter('PHP_AUTH_USER', $request);
        }
    }
}
