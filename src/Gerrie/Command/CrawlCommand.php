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
use Gerrie\Component\Configuration\CommandConfiguration;
use Gerrie\Component\Database\Database;
use Gerrie\API\DataService\DataServiceFactory;
use Gerrie\Component\Console\InputExtendedInterface;
use Gerrie\Service\DatabaseService;
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
        $this->addSetupDatabaseOption();
        $this->addDebugOption();
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
        /** @var InputExtendedInterface $input */
        $this->outputStartMessage($output);

        // If we enable the "setup-database-tables" setting, we will check if the necessary tables
        // are already there. If not we will try to setup / create them.
        // Try, because this process can fail due to missing access rights of the database user.
        // If the user got the needed rights, everything will work fine ;)
        if ($input->getOption('setup-database-tables') === true) {
            $databaseService = new DatabaseService($this->database, $output);
            $databaseService->setupDatabaseTables();
        }

        // Start the importer for each configured project
        $gerritSystems = $this->configuration->getConfigurationValue('Gerrit');
        $defaultSSHKeyFile = $this->configuration->getConfigurationValue('SSH.KeyFile');

        foreach ($gerritSystems as $name => $gerrieProject) {
            $gerritSystem = [
                'Name' => $name
            ];

            foreach ($gerrieProject as $gerritInstance) {

                // TODO Extract this Instance Key part here. This is the same as in "ListProjectsCommand".
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
                $gerrie = new Gerrie($this->database, $dataService, $gerritSystem);
                $gerrie->setOutput($output);
                if ($input->getOption('debug') === true) {
                    $gerrie->enableDebugFunctionality();
                } else {
                    $gerrie->disableDebugFunctionality();
                }

                // Start the crawling action
                $gerrie->crawl();
            }
        }

        $this->outputEndMessage($output);
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