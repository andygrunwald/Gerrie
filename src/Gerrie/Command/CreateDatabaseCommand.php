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

use Gerrie\Component\Configuration\Configuration;
use Gerrie\Component\Database\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDatabaseCommand extends Command
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
            ->setName('gerrie:create-database')
            ->setDescription('Creates the required database scheme')
            ->addOption('configFile', 'c', InputOption::VALUE_REQUIRED, 'Path to configuration file', CONFIG_FILE);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->configuration = new Configuration($input->getOption('configFile'));
        $databaseConfig = $this->configuration->getConfigurationValue('Database');

        $this->database = new Database($databaseConfig);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->outputStartMessage($output);
        $output->writeln('');

        $databaseHandle = $this->database->getDatabaseConnection();
        $tableDefinition = $this->database->getTableDefinition();

        $tables = array_keys($tableDefinition);
        foreach ($tables as $tableName) {
            $output->writeln('<info>Table "' . $tableName . '"</info>');

            $statement = $databaseHandle->prepare('SHOW TABLES LIKE :table');
            $statement->bindParam(':table', $tableName, \PDO::PARAM_STR);
            $statement->execute();

            if ($statement->rowCount() == 1) {
                $output->writeln('<info>=> Exists. Skip it</info>');
                continue;
            }

            // Table does not exists. Try to create it
            $createTableResult = $databaseHandle->query($tableDefinition[$tableName]);

            if ($createTableResult === false) {
                $databaseError = $databaseHandle->errorInfo();
                $message = 'Table "%s" could not be created. %s (%s)';
                $message = sprintf($message, $tableName, $databaseError[2], $databaseError[1]);
                throw new \Exception($message, 1398100879);

            } else {
                $output->writeln('<info>Not exists. Created</info>');
            }
        }

        $this->outputEndMessage($output);
    }

    protected function outputStartMessage(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<comment>Starting application "' . self::COMMAND_NAME . '"</comment>');
    }

    protected function outputEndMessage(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<comment>Application "' . self::COMMAND_NAME . '" finished</comment>');
        $output->writeln('');
    }
}