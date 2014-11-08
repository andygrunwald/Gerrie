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

use Gerrie\Component\Console\InputExtendedInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigurationFactory
{
    /**
     * Creates a configuration based on a YAML configuration file.
     *
     * @param string $configFile
     * @return Configuration
     */
    public static function getConfigurationByConfigFile($configFile)
    {
        if (file_exists($configFile) === false) {
            $message = 'Configuration file "%s" not found or accessible.';
            $message = sprintf($message, $configFile);
            throw new \RuntimeException($message, 1415381521);
        }

        $config = Yaml::parse($configFile);
        $configuration = new Configuration($config);

        return $configuration;
    }

    /**
     * Creates a configuration based on a YAML configuration file and command line options.
     * The command line options will be merged into the configuration.
     *
     * The command line options got a higher priority as the configuration file.
     *
     * @param string $configFile
     * @param InputExtendedInterface $input
     * @return Configuration
     */
    public static function getConfigurationByConfigFileAndCommandOptions($configFile, InputExtendedInterface $input)
    {
        $configuration = self::getConfigurationByConfigFile($configFile);
        $configuration = self::mergeCommandOptionsIntoConfiguration($configuration, $input);

        return $configuration;
    }

    /**
     * Merges the command line options into a existing configuration.
     *
     * @param Configuration $config
     * @param InputExtendedInterface $input
     * @return Configuration
     */
    protected static function mergeCommandOptionsIntoConfiguration(Configuration $config, InputExtendedInterface $input)
    {
        $configurationMapping = [
            // Database credentials
            'database-host' => 'Database.Host',
            'database-user' => 'Database.Username',
            'database-pass' => 'Database.Password',
            'database-port' => 'Database.Port',
            'database-name' => 'Database.Name',
            // SSH settings
            'ssh-key'       => 'SSH.KeyFile',
        ];

        foreach ($configurationMapping as $optionName => $configName) {
            if ($input->isOptionSet($optionName) === true) {
                $config->setConfigurationValue($configName, $input->getOption($optionName));
            }
        }

        return $config;
    }
}