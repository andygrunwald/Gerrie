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

use Gerrie\Component\Configuration\ConfigurationFactory;
use Gerrie\Component\Configuration\CommandConfiguration;
use Gerrie\Component\Database\Database;
use Gerrie\Service\DatabaseService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDatabaseCommand extends GerrieBaseCommand
{

    /**
     * Command name
     *
     * @var string
     */
    const COMMAND_NAME = 'Create Database';

    /**
     * Database object
     *
     * @var \Gerrie\Service\DatabaseService
     */
    protected $databaseService = null;

    protected function configure()
    {
        $this
            ->setName('gerrie:create-database')
            ->setDescription('Creates the required database scheme');

        $this->addConfigFileOption();
        $this->addDatabaseOptions();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        /** @var InputExtendedInterface $input */

        $configFile = $input->getOption('config-file');
        $configuration = ConfigurationFactory::getConfigurationByConfigFileAndCommandOptionsAndArguments($configFile, $input);

        $databaseConfig = $configuration->getConfigurationValue('Database');
        $database = new Database($databaseConfig);

        $this->databaseService = new DatabaseService($database, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<comment>Starting application "' . self::COMMAND_NAME . '"</comment>');
        $output->writeln('');

        $this->databaseService->setupDatabaseTables();

        $output->writeln('');
        $output->writeln('<comment>Application "' . self::COMMAND_NAME . '" finished</comment>');
        $output->writeln('');
    }
}