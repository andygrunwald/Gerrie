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
 * Check if the config file exists.
 *
 * @author Andreas Grunwald <andygrunwald@gmail.com>
 */
class ConfigFileCheck implements CheckInterface
{

    /**
     * Filename of config
     *
     * @var string
     */
    private $filename = '';

    /**
     * Bool if the file is readable
     *
     * @var bool
     */
    private $isReadable;

    /**
     * Bool if the file was found
     *
     * @var bool
     */
    private $isFound;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Executes the check itselfs
     *
     * @return boolean
     */
    public function check()
    {
        $this->isFound = file_exists($this->filename);
        $this->isReadable = is_readable($this->filename);

        $result = $this->isFound && $this->isReadable;

        return $result;
    }

    /**
     * Returns the message, if the check succeeded
     *
     * @return string
     */
    public function getSuccessMessage()
    {
        $message = 'Config file "%s" was found and is readable.';
        $message = sprintf($message, $this->filename);
        return $message;
    }

    /**
     * Returns the message, if the check fails
     *
     * @return string
     */
    public function getFailureMessage()
    {
        $message = '';
        if ($this->isFound === false && $this->isReadable === false) {
            $message  = 'Config file "%s" was not found. ';
            $message .= 'Please provide the correct path or all settings via command options.';

        } elseif ($this->isFound === true && $this->isReadable === false) {
            $message  = 'Config file "%s" was found, but is not readable. ';
            $message .= 'Please change ownerships or all settings via command options.';
        }

        $message = sprintf($message, $this->filename);

        return $message;
    }

    /**
     * Returns if this check is optional or required.
     *
     * @return bool
     */
    public function isOptional()
    {
        return true;
    }
}
