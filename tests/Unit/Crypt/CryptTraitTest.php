<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

use BurningCloudSystem\OAuth2\Server\Crypt\CryptTrait;
use Defuse\Crypto\Key;
use PHPUnit\Framework\TestCase;

class CryptTraitTest extends TestCase
 {
    protected function setUp() : void
    {
    }

    /**
     * @param string $data
     * @return void
     * 
     * @testWith ["test"]
     */
    public function testEncryptAndDecrypt(string $data)
    {
        $crypt = new CryptBeings();
        $crypt->setEncryptionKey(Key::createNewRandomKey());

        $before = $crypt->encryptBase($data);
        $this->assertNotEquals($data, $before);

        $data2 = $crypt->decryptBase($before);
        $this->assertEquals($data, $data2);
    }

    /**
     * @return void
     * 
     * @testWith ["test"]
     */
    public function testEncryptThrow(string $data)
    {
        $crypt = new CryptBeings();
        $crypt->setDummyKey("KEY");
        $this->expectException(LogicException::class);

        $before = $crypt->encryptBase($data);
    }

    /**
     * @return void
     * 
     * @testWith ["def5020032b0e125d1a98125a0077f9b8e0aecf1010fa8f6ddc44d6dd1fd45ec8fa1cb02449a93b4e8063a357ee36aac34e05d5d112198de59c3e1dd7446c22df975c8c7bc5d23a7f19ea363f304a0699d5801d4a2648fdb"]
     */
    public function testDecrypt(string $data)
    {
        $crypt = new CryptBeings();
        $crypt->setDummyKey("KEY");
        $this->expectException(LogicException::class);

        $before = $crypt->decryptBase($data);
    }

    protected function tearDown() : void
    {
    }
 }

class CryptBeings 
{
    use CryptTrait;

    public function encryptBase(string $unencryptedData) : string
    {
        return $this->encrypt($unencryptedData);
    }

    public function decryptBase(string $encryptedData) : string
    {
        return $this->decrypt($encryptedData);
    }

    public function setDummyKey(string $dummyKey) : void
    {
        $this->encryptionKey = $dummyKey;
    }
}
