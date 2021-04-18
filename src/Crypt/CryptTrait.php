<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Crypt;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Exception;
use LogicException;

trait CryptTrait
{
    protected $encryptionKey;

    /**
     * Encrypt data with encryptionKey.
     *
     * @param string $unencryptedData
     * 
     * @throws LogicException
     * 
     * @return string
     */
    protected function encrypt(string $unencryptedData) : string
    {
        try {
            if ($this->encryptionKey instanceof Key)
            {
                return Crypto::encrypt($unencryptedData, $this->encryptionKey);
            }

            if (is_string($this->encryptionKey)) 
            {
                return Crypto::encryptWithPassword($unencryptedData, $this->encryptionKey);
            }

            throw new LogicException('Encryption key not set when attempting to encrypt');
        } 
        catch(Exception $e)
        {
            throw new LogicException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Decrypt data with encryptionKey.
     *
     * @param string $encryptedData
     * @return string
     */
    protected function decrypt(string $encryptedData) : string
    {
        try {
            if ($this->encryptionKey instanceof Key)
            {
                return Crypto::decrypt($encryptedData, $this->encryptionKey);
            }

            if (is_string($this->encryptionKey)) 
            {
                return Crypto::decryptWithPassword($encryptedData, $this->encryptionKey);
            }

            throw new LogicException('Encryption key not set when attempting to decrypt');
        } 
        catch(Exception $e)
        {
            throw new LogicException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Set the encryption key.
     *
     * @param string|Key $key
     * @return void
     */
    public function setEncryptionKey($key) : void
    {
        $this->encryptionKey = $key;
    }
}