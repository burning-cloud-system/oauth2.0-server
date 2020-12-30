<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Crypt;

use LogicException;
use RuntimeException;

class CryptKey
{
    const RSA_KEY_PATTERN =
        '/^(-----BEGIN (RSA )?(PUBLIC|PRIVATE) KEY-----)\R.*(-----END (RSA )?(PUBLIC|PRIVATE) KEY-----)\R?$/s';
    
    /**
     * @var string
     */
    protected string $keyPath;

    /**
     * @var string|null
     */
    protected ?string $passPhrase;

    /**
     * Construct
     *
     * @param string $keyPath
     * @param string|null $passPhrase
     * @param boolean $keyPermissionsCheck
     */
    public function __construct(string $keyPath, ?string $passPhrase = null, bool $keyPermissionsCheck = true)
    {
        if ($rsaMatch = preg_match(static::RSA_KEY_PATTERN, $keyPath))
        {
            $keyPath = $this->saveKeyToFile($keyPath);
        }
        elseif ($rsaMatch === false)
        {
            throw new RuntimeException(sprintf('PCRE error [%d] encountered during key match attempt', preg_last_error()));
        }

        if (strpos($keyPath, 'file://') !== 0)
        {
            $keyPath = 'file://' . $keyPath;
        }

        if (!file_exists($keyPath) || !is_readable($keyPath))
        {
            throw new LogicException(sprintf('Key path "%s" does not exist or is not readable', $keyPath));
        }

        if ($keyPermissionsCheck === true)
        {
            $keyPathPerms = decoct(fileperms($keyPath) & 0777);
            if (in_array($keyPathPerms, ['400', '440', '600', '640', '660'], true) === false)
            {
                trigger_error(sprintf('Key file "%s" permissions are not correct, recommend changing to 600 or 660 instead of %s', 
                    $keyPath,
                    $keyPathPerms), E_USER_NOTICE);
            }
        }

        $this->keyPath    = $keyPath;
        $this->passPhrase = $passPhrase;
    }

    /**
     * Save key to file.
     *
     * @param string $key
     * @return void
     */
    private function saveKeyToFile(string $key)
    {
        $tmpDir = sys_get_temp_dir();
        $keyPath = $tmpDir . '/' . sha1($key) . '.key';

        if (file_exists($keyPath))
        {
            return 'file://' . $keyPath;
        }

        if (file_put_contents($keyPath, $key) === false)
        {
            throw new RuntimeException(sprintf('Unable to write key file to temporary directory "%s"', $tmpDir));
        }

        if (chmod($keyPath, 0600) === false)
        {
            throw new RuntimeException(sprintf('The key file "%s" file mode could not be changed with chmod to 600', $keyPath));
        }

        return 'file://' . $keyPath;
    }

    /**
     * Retrieve key path.
     *
     * @return string
     */
    public function getKeyPath() : string
    {
        return $this->keyPath;
    }

    /**
     * Retrieve key pass phrase
     *
     * @return string
     */
    public function getPassPhrase() : string
    {
        return $this->passPhrase;
    }
}