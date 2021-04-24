<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Request\Parame;

use Psr\Http\Message\ServerRequestInterface;

class AuthorizationResponseTypeParame extends ResponseTypeParame
{
    /**
     * tHE CODE CHALLENGE 
     * @var string
     */
    public const CODE_CHALLENGE = "code_challenge";

    /**
     * The code challenge method
     * @var string
     */
    public const CODE_CHALLENGE_METHOD = "code_challenge_method";

    /**
     * The code challenge property used in the request 
     *
     * @var  string|null
     */
    public ?string $codeChallenge;

    /**
     * The code challenge method property used in the request 
     * @var string
     */
    public string $codeChallengeMethod;

    /**
     * Bind request parameter.
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function bindParame(ServerRequestInterface $request)
    {
        parent::bindParame($request);
        
        // set request parameter.
        $this->codeChallenge       = $this->getQueryStringParameter(self::CODE_CHALLENGE, $request);
        $this->codeChallengeMethod = $this->getQueryStringParameter(self::CODE_CHALLENGE_METHOD, $request, 'plain');

    }
  
}

