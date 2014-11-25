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
     * Creates a configuration based on a YAML configuration file, command line options and command line arguments.
     * The command line options will be merged into the configuration.
     * The command line arguments will be merged into the configuration as well.
     *
     * The command line options got a higher priority as the configuration file.
     * The command line arguments will be added to the configuration file and not overwritten.
     *
     * @param string $configFile
     * @param InputExtendedInterface $input
     * @return Configuration
     */
    public static function getConfigurationByConfigFileAndCommandOptionsAndArguments($configFile, InputExtendedInterface $input)
    {
        if ($input->isOptionSet('config-file') === true) {
            $configuration = self::getConfigurationByConfigFile($configFile);
        } else {
            $configuration = new Configuration();
        }

        $configuration = self::mergeCommandOptionsIntoConfiguration($configuration, $input);
        $configuration = self::mergeCommandArgumentsIntoConfiguration($configuration, $input);

        return $configuration;
    }

    /**
     * Merges the command line options into a existing configuration.
     *
     * E.g.
     *  * Database credentials
     *  * SSH settings
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

            // If the value is not set / available, set this to nothing
            } elseif (!$config->getConfigurationValue($configName)) {
                $config->setConfigurationValue($configName, null);
            }
        }

        return $config;
    }

    /**
     * Merges the command line arguments into a existing configuration.
     *
     * E.g.
     *  * Instances
     *
     * @param Configuration $config
     * @param InputExtendedInterface $input
     * @return Configuration
     */
    protected static function mergeCommandArgumentsIntoConfiguration(Configuration $config, InputExtendedInterface $input)
    {
        if ($input->hasArgument('instances') === false) {
            return $config;
        }

        $argumentInstances = $input->getArgument('instances');

        if (count($argumentInstances) === 0) {
            return $config;
        }

        // Gerrie is a reserved keyword for project names
        $config->setConfigurationValue('Gerrit.Gerrie', $argumentInstances);

        return $config;
    }
}