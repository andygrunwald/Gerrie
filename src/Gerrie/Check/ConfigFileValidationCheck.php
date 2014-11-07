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

use Gerrie\Component\Configuration\Configuration;

/**
 * Check if the config is valid.
 *
 * @author Andreas Grunwald <andygrunwald@gmail.com>
 */
class ConfigFileValidationCheck implements CheckInterface
{

    /**
     * Configuration object
     *
     * @var Configuration
     */
    private $configuration = '';

    /**
     * Set of missing configuration keys
     *
     * @var array
     */
    private $missingConfiguration = [];

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Executes the check itselfs
     *
     * @return boolean
     */
    public function check()
    {
        $result = true;

        $configurationSettings = [
            'Database.Host',
            'Database.Username',
            'Database.Password',
            'Database.Port',
            'Database.Name',
        ];

        foreach ($configurationSettings as $setting) {
            if ($this->configuration->hasConfigurationKey($setting) === false) {
                $this->missingConfiguration[] = $setting;
                $result = false;
            }
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
        $message = 'Config file is configured properly.';
        return $message;
    }

    /**
     * Returns the message, if the check fails
     *
     * @return string
     */
    public function getFailureMessage()
    {
        $missingKeys = implode(', ', $this->missingConfiguration);

        $message  = 'The configuration is not complete. ';
        $message .= 'Missing keys are %s. Please provide them as command options.';
        $message = sprintf($message, $missingKeys);

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
