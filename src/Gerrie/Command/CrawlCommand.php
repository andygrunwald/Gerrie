<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Command;

use Gerrie\Gerrie;
use Gerrie\Component\Configuration\ConfigurationFactory;
use Gerrie\Component\Database\Database;
use Gerrie\Component\DataService\DataServiceFactory;
use Gerrie\Component\Console\InputExtendedInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlCommand extends GerrieBaseCommand
{

    /**
     * Command name
     *
     * @var string
     */
    const COMMAND_NAME = 'Crawl';

    /**
     * Database object
     *
     * @var \Gerrie\Component\Database\Database
     */
    protected $database = null;

    /**
     * Configuration object
     *
     * @var \Gerrie\Component\Configuration\Configuration
     */
    protected $configuration = null;

    protected function configure()
    {
        $this
            ->setName('gerrie:crawl')
            ->setDescription('Crawls a Gerrit review system and stores the into a database');
        $this->addConfigFileOption();
        $this->addDatabaseOptions();
        $this->addSSHKeyOption();
        $this->addInstancesArgument();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        /** @var InputExtendedInterface $input */

        $configFile = $input->getOption('config-file');
        $this->configuration = ConfigurationFactory::getConfigurationByConfigFileAndCommandOptionsAndArguments($configFile, $input);

        $databaseConfig = $this->configuration->getConfigurationValue('Database');
        $this->database = new Database($databaseConfig);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->outputStartMessage($output);

        $this->setupDatabaseCommand($output);

        // Start the importer for each configured project
        $gerritSystems = $this->configuration->getConfigurationValue('Gerrit');
        $defaultSSHKeyFile = $this->configuration->getConfigurationValue('SSH.KeyFile');

        foreach ($gerritSystems as $name => $gerrieProject) {
            $gerritSystem['Name'] = $name;

            foreach ($gerrieProject as $gerritInstance) {

                // Get instance url
                // If the instance is a string, we only got a url path like scheme://user@url:port/
                if (is_string($gerritInstance)) {
                    $instanceConfig = [
                        'Instance' => $gerritInstance,
                        'KeyFile' => $defaultSSHKeyFile
                    ];

                // If the instance is an array, we get a key => value structure with an Instance key
                } elseif (is_array($gerritInstance) && isset($gerritInstance['Instance'])) {
                    $instanceConfig = [
                        'Instance' => $gerritInstance['Instance'],
                        'KeyFile' => $defaultSSHKeyFile
                    ];

                    if (array_key_exists('KeyFile', $gerritInstance) === true) {
                        $instanceConfig['KeyFile'] = $gerritInstance['KeyFile'];
                    }
                } else {
                    throw new \RuntimeException('No Gerrit instance config given', 1415451921);
                }

                $dataService = DataServiceFactory::getDataService($instanceConfig);

                // Bootstrap the importer
                $gerrit = new Gerrie($this->database, $dataService, $gerritSystem);
                $gerrit->setOutput($output);

                // Start the crawling action
                $gerrit->crawl();
            }
        }

        $this->outputEndMessage($output);
    }

    /**
     * Executed the "gerrie:create-database" command to setup the database.
     *
     * @param OutputInterface $output
     * @throws \Exception
     * @return void
     */
    protected function setupDatabaseCommand(OutputInterface $output)
    {
        // Run gerrie:create-database-Command
        $output->writeln('<info>Check database ...</info>');

        $command = $this->getApplication()->find('gerrie:create-database');
        $arguments = array(
            'command' => 'gerrie:create-database',
        );
        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }

    protected function outputStartMessage(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<comment>Starting application "' . self::COMMAND_NAME . '"</comment>');
        $output->writeln('');
    }

    protected function outputEndMessage(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<comment>Application "' . self::COMMAND_NAME . '" finished</comment>');
        $output->writeln('');
    }
}