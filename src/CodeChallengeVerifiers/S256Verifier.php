<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\CodeChallengeVerifiers;

class S256Verifier implements CodeChallengeVerifierInterface
{
    /**
     * Return code challenge method.
     *
     * @return string
     */
    public function getMethod() : string
    {
        return 'S256';
    }

    /**
     * Verify the code challenge.
     *
     * @param string $codeVerifier
     * @param string $codeChallenge
     * @return boolean
     */
    public function verifyCodeChallenge(string $codeVerifier, string $codeChallenge) : bool
    {
        $codeVerifier = strtr(rtrim(base64_encode(hash('sha256', $codeVerifier, true)), '='), '+/', '-_');
        return hash_equals($codeVerifier, $codeChallenge);
    }
}
