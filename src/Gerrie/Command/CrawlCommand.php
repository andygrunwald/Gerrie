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
use Gerrie\Component\Configuration\Configuration;
use Gerrie\Component\Database\Database;
use Gerrie\Helper\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlCommand extends Command
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
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->configuration = new Configuration(CONFIG_FILE);
        $databaseConfig = $this->configuration->getConfigurationValue('Database');

        $this->database = new Database($databaseConfig);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->outputStartMessage($output);

        // Run gerrie:create-database-Command
        $output->writeln('<info>Check database ...</info>');

        $command = $this->getApplication()->find('gerrie:create-database');
        $arguments = array(
            'command' => 'gerrie:create-database',
        );
        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        // Start the importer for each configured project
        $gerritSystems = $this->configuration->getConfigurationValue('Gerrit');

        foreach ($gerritSystems as $name => $gerritSystem) {
            $gerritSystem['Name'] = $name;

            $dataService = Factory::getDataService($this->configuration, $name);

            // Bootstrap the importer
            $gerrit = new Gerrie($this->database, $dataService, $gerritSystem);
            $gerrit->setOutput($output);

            // Start the crawling action
            $gerrit->crawl();
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