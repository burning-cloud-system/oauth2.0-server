<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

use BurningCloudSystem\OAuth2\Server\CodeChallengeVerifiers\CodeChallengeVerifierInterface;
use BurningCloudSystem\OAuth2\Server\CodeChallengeVerifiers\PlainVerifier;
use PHPUnit\Framework\TestCase;

class PlainVerifierTest extends TestCase
{
    public CodeChallengeVerifierInterface $verifier;

    protected function setUp() : void
    {
        $this->verifier = new PlainVerifier();
    }

    /**
     *
     * @param string $method
     * @return void
     * 
     * @testWith ["plain"]
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
        $this->assertTrue($this->verifier->verifyCodeChallenge($codeVerifier, $codeChallenge));
    }

    protected function tearDown() : void
    {
    }
}
