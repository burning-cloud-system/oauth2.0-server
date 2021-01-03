<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

use BurningCloudSystem\OAuth2\Server\Crypt\CryptKey;
use PHPUnit\Framework\TestCase;

class CryptKeyTest extends TestCase
{
    /**
     * @var string
     */
    private string $keyStr = <<< EOF
    -----BEGIN RSA PRIVATE KEY-----
    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
    -----END RSA PRIVATE KEY-----
    EOF;

    /**
     * @var string
     */
    private string $fileName;

    protected function setUp() : void
    {
        $privateKey = new CryptKey($this->keyStr, null, false);
        $this->fileName = $privateKey->getKeyPath();
    }

    /**
     * Command: .\vendor\bin\phpunit.bat tests\Unit\Crypt\CryptKeyTest.php
     *
     * @param string $passPhrase
     * @return void
     * 
     * @testWith ["abcd"]
     */
    public function testCryptKey(string $passPhrase)
    {
        $privateKey = new CryptKey($this->fileName, $passPhrase, false);

        $this->assertEquals($this->fileName, $privateKey->getKeyPath());

        $this->assertEquals($passPhrase, $privateKey->getPassPhrase());
    }

    /**
     * @return void
     */
    public function testCryptKeyPath()
    {
        $fileName = str_replace("file://", "", $this->fileName);

        $privateKey = new CryptKey($fileName, 'passPhrase', false);

        $this->assertEquals($this->fileName, $privateKey->getKeyPath());
    }

    /**
     * @param string $absolutePath
     * @return void
     * 
     * @testWith ["D:\\Project\\PhpProject\\ComposerProject\\01.Source\\01.Workspace\\oauth2.0-server\\tests"]
     */
    public function testCreateKey(string $absolutePath)
    {
        $privateKey = new CryptKey($this->keyStr, 'passPhrase', false, $absolutePath);

        $this->assertFileExists($privateKey->getKeyPath());

        if (file_exists($privateKey->getKeyPath()))
        {
            unlink($privateKey->getKeyPath());
        }
    }

    protected function tearDown() : void
    {
        if (file_exists($this->fileName)) 
        {
            unlink($this->fileName);
        }
    }
}
