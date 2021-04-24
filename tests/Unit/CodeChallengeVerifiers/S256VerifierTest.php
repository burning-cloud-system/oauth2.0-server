<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

use BurningCloudSystem\OAuth2\Server\CodeChallengeVerifiers\CodeChallengeVerifierInterface;
use BurningCloudSystem\OAuth2\Server\CodeChallengeVerifiers\S256Verifier;
use PHPUnit\Framework\TestCase;

class S256VerifierTest extends TestCase
{
    public CodeChallengeVerifierInterface $verifier;

    protected function setUp() : void
    {
        $this->verifier = new S256Verifier();
    }

    /**
     *
     * @param string $method
     * @return void
     * 
     * @testWith ["S256"]
     */
    public function testGetMethod(string $method)
    {
        $this->assertEquals($method, $this->verifier->getMethod());
    }

    /**
     * @param string $codeVerifier
     * @param string $codeChallenge
     * @return void
     * 
     * @testWith ["test", "test"]
     */
    public function testVerifyCodeChallenge(string $codeVerifier, string $codeChallenge)
    {
        $codeChallenge = strtr(\rtrim(\base64_encode(\hash('sha256', $codeVerifier, true)), '='), '+/', '-_');
        
        $this->assertTrue($this->verifier->verifyCodeChallenge($codeVerifier, $codeChallenge));
    }

    protected function tearDown() : void
    {
    }
}
