<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020-2021 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\ResponseTypes;

use Psr\Http\Message\ResponseInterface;

class RedirectResponse extends AbstractResponseType
{
    /**
     * @var string
     */
    private string $redirectUri;

    /**
     * Set redirect uri.
     *
     * @param string $redirectUri
     * @return void
     */
    public function setRedirectUri(string $redirectUri) : void
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function generateHttpResponse(ResponseInterface $response) : ResponseInterface
    {
        return $response->withStatus(302)->withHeader('Location', $this->redirectUri);
    }
}
