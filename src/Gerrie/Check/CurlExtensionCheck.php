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
 * Check if PHP extension "curl" is installed.
 *
 * @author Andreas Grunwald <andygrunwald@gmail.com>
 */
class CurlExtensionCheck implements CheckInterface
{
    /**
     * Version of curl extension
     *
     * @var string
     */
    private $extensionVersion = '';

    /**
     * Executes the check itselfs
     *
     * @return boolean
     */
    public function check()
    {
        $extensionName = 'curl';
        $result = extension_loaded($extensionName);

        if ($result === true) {
            $curlVersion = curl_version();
            $this->extensionVersion = $curlVersion['version'];
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
        $message = 'PHP-Extension "curl" is installed and usable with "curl" in v%s.';
        $message = sprintf($message, $this->extensionVersion);
        return $message;
    }

    /**
     * Returns the message, if the check fails
     *
     * @return string
     */
    public function getFailureMessage()
    {
        $message = 'PHP-Extension "curl" is not installed. Please install PHP-Extension "curl".';
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
