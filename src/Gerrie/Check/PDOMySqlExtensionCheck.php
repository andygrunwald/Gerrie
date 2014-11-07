<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Check;

/**
 * Check if PHP extensions "PDO" and "pdo_mysql" are installed.
 *
 * @author Andreas Grunwald <andygrunwald@gmail.com>
 */
class PDOMySqlExtensionCheck implements CheckInterface
{
    /**
     * Version of PDO extension
     *
     * @var string
     */
    private $pdoVersion = '';

    /**
     * Version of pdo_mysql extension
     *
     * @var string
     */
    private $pdoMySqlVersion = '';

    /**
     * Executes the check itselfs
     *
     * @return boolean
     */
    public function check()
    {
        $pdoResult = extension_loaded('PDO');
        $pdoMySqlResult = extension_loaded('pdo_mysql');

        $result = ($pdoResult === true && $pdoMySqlResult === true);

        if ($pdoResult === true) {
            $this->pdoVersion = phpversion('PDO');
        }

        if ($pdoMySqlResult === true) {
            $this->pdoMySqlVersion = phpversion('pdo_mysql');
        }

        return $result;
    }

    /**
     * Returns the message, if the check succeeded
     *
     * @return string
     */
    public function getSuccessMessage()
    {
        $message = 'PHP-Extensions "PDO" (v%s) and "pdo_mysql" (v%s) are installed and usable.';
        $message = sprintf($message, $this->pdoVersion, $this->pdoMySqlVersion);
        return $message;
    }

    /**
     * Returns the message, if the check fails
     *
     * @return string
     */
    public function getFailureMessage()
    {
        if (!$this->pdoVersion && !$this->pdoMySqlVersion) {
            $message = 'PHP-Extensions "PDO" and "pdo_mysql" are not installed. Please install both.';

        } elseif ($this->pdoVersion && !$this->pdoMySqlVersion) {
            $message = 'PHP-Extension "PDO" (v%s) is installed, but "pdo_mysql" not. Please install it.';
            $message = sprintf($message, $this->pdoVersion);
        }

        return $message;
    }

    /**
     * Returns if this check is optional or required.
     *
     * @return bool
     */
    public function isOptional()
    {
        return false;
    }
}
