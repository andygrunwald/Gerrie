<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Component\Configuration;

class Configuration
{
    /**
     * Delimiter for configuration keys
     *
     * @var string
     */
    const DELIMITER = '.';

    /**
     * The configuration storage
     *
     * @var array
     */
    protected $config = [];

    /**
     * Bootstraps the configuration
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfiguration($config);
    }

    /**
     * Returns the configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Sets the configuration
     *
     * @param array $config
     * @return array
     */
    public function setConfiguration(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Sets a single configuration value
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setConfigurationValue($key, $value)
    {
        $completeConfiguration = $this->getConfiguration();
        $configuration = &$completeConfiguration;

        $pathParts = explode(self::DELIMITER, $key);
        foreach ($pathParts as $pathPart) {

            // If the current configuration is not an array,
            // but the client want to set a value, just overwrite the current value
            if (is_array($configuration) === false) {
                $configuration = [];
            }
            $configuration = &$configuration[ucfirst($pathPart)];
        }

        $configuration = $value;
        $this->setConfiguration($completeConfiguration);
    }

    /**
     * Returns a single configuration value
     *
     * @param string $key
     * @return mixed
     */
    public function getConfigurationValue($key)
    {
        $configuration = $this->getConfiguration();

        $pathParts = explode(self::DELIMITER, $key);
        foreach ($pathParts as $pathPart) {
            if (isset($configuration[ucfirst($pathPart)]) === true) {
                $configuration = $configuration[ucfirst($pathPart)];
            } else {
                $configuration = null;
            }
        }

        return $configuration;
    }

    /**
     * Checks if the configuration got the incoming key
     *
     * @param string $key
     * @return bool
     */
    public function hasConfigurationKey($key)
    {
        $result = true;
        $configuration = $this->getConfiguration();

        $pathParts = explode(self::DELIMITER, $key);
        foreach ($pathParts as $pathPart) {
            if (array_key_exists(ucfirst($pathPart), $configuration) === false) {
                $result = false;
                break;
            } else {
                $configuration = $configuration[ucfirst($pathPart)];
            }
        }

        return $result;
    }
}