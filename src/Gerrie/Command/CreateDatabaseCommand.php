<?php

namespace Gerrie\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Gerrie\Helper\Configuration;
use Gerrie\Helper\Database;

class CreateDatabaseCommand extends Command {

	/**
	 * Command name
	 *
	 * @var string
	 */
	const COMMAND_NAME = 'Create Database';

	/**
	 * Database object
	 *
	 * @var \Gerrie\Helper\Database
	 */
	protected $database = null;

	/**
	 * Configuration object
	 *
	 * @var \Gerrie\Helper\Configuration
	 */
	protected $configuration = null;

	protected function configure() {
		$this
			->setName('gerrie:create-database')
			->setDescription('Creates the required database scheme');
	}

	protected function initialize(InputInterface $input, OutputInterface $output) {
		$this->configuration = new Configuration();
		$databaseConfig = $this->configuration->getConfigurationValue('Database');

		$this->database = new Database($databaseConfig);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

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
				throw new \Exception('Table "' . $tableName . '" could not be created. ' . $databaseError[2] . ' (' . $databaseError[1] . ')');

			} else {
				$output->writeln('<info>Not exists. Created</info>');
			}
		}

		$this->outputEndMessage($output);
	}

	protected function outputStartMessage(OutputInterface $output) {
		$output->writeln('');
		$output->writeln('<comment>Starting application "' . self::COMMAND_NAME . '"</comment>');
	}

	protected function outputEndMessage(OutputInterface $output) {
		$output->writeln('');
		$output->writeln('<comment>Application "' . self::COMMAND_NAME . '" finished</comment>');
		$output->writeln('');
	}
}