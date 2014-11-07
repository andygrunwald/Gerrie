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
 * Check if ssh is installed.
 *
 * @author Andreas Grunwald <andygrunwald@gmail.com>
 */
class SSHCheck implements CheckInterface
{
    /**
     * Version of ssh
     *
     * @var string
     */
    private $sshVersion = '';

    /**
     * Executes the check itselfs
     *
     * @return boolean
     */
    public function check()
    {
        $returnVar = 0;
        $output = [];
        $sshVersion = exec('ssh -V 2>&1', $output, $returnVar);

        $result = false;
        if ($returnVar === 0) {
            $result = true;
            $this->sshVersion = $sshVersion;
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
        $message = '"ssh" is installed and usable in version "%s".';
        $message = sprintf($message, $this->sshVersion);
        return $message;
    }

    /**
     * Returns the message, if the check fails
     *
     * @return string
     */
    public function getFailureMessage()
    {
        $message = '"ssh" is not installed. Please install "ssh".';
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
