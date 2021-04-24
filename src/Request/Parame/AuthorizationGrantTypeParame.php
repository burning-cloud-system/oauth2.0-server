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

class AuthorizationGrantTypeParame extends GrantTypeParame
{
    /**
     * The code
     * 
     * @var string
     */
    public const CODE = 'code';

    /**
     * The code verifier
     * 
     * @var string
     */
    public const CODE_VERIFIER = 'code_verifier';

    /**
     * The code property
     * 
     * @var string
     */
    public string $code;

    /**
     * The code verifier property
     *
     * @var string
     */
    public string $codeVerifier;

    /**
     * {@inheritDoc}
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function bindParame(ServerRequestInterface $request)
    {
        parent::bindParame($request);

        // set request parameter.
        $this->code         = $this->getRequestParameter(self::CODE,          $request);
        $this->codeVerifier = $this->getRequestParameter(self::CODE_VERIFIER, $request);
    }
}

