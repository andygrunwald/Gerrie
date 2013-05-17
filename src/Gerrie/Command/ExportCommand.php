<?php

namespace Gerrie\Command;

use Gerrie\DataService\HTTP;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Gerrie\Helper\Configuration;
use Gerrie\Helper\Database;
use Gerrie\Helper\SSH;
use Gerrie\Export\Gerrit;

class ExportCommand extends Command {

	/**
	 * Command name
	 *
	 * @var string
	 */
	const COMMAND_NAME = 'Export';

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
			->setName('gerrie:export')
			->setDescription('Exports data from a Gerrit review system into a database');
	}

	protected function initialize(InputInterface $input, OutputInterface $output) {
		$this->configuration = new Configuration();
		$databaseConfig = $this->configuration->getConfigurationValue('Database');

		$this->database = new Database($databaseConfig);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
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

			$dataService = $this->getDataService($gerritSystem);

			// Bootstrap the importer
			$gerrit = new Gerrit($this->database, $dataService, $gerritSystem);
			$gerrit->setOutput($output);

			// Start the export action
			$gerrit->export();
		}

		$this->outputEndMessage($output);
	}

	protected function getDataService(array $config) {
		$dataServiceConfig = strtoupper($config['DataService']);

		switch (strtoupper($config['DataService'])) {
			case 'SSH':
				$dataService = $this->bootstrapSSHDataService($config);
				break;
			case 'HTTP':
				$dataService = $this->bootstrapHTTPDataService($config);
				break;
			default:
				throw new \Exception('Data service "' . $dataServiceConfig . '" not supported', 1364130057);
		}

		return $dataService;
	}

	protected function bootstrapSSHDataService(array $config) {
		$sshExec = $this->configuration->getConfigurationValue('Executable.SSH');
		$ssh = new SSH($sshExec, $config['SSH']);

		$dataService = new \Gerrie\DataService\SSH($ssh, $config['SSH']);

		return $dataService;
	}

	protected function bootstrapHTTPDataService(array $config) {
		$restClient = \Gerrie\Helper\Factory::getHTTPClientInstance($config);
		$dataService = new \Gerrie\DataService\HTTP($restClient, $config['HTTP']);

		return $dataService;
	}

	protected function outputStartMessage(OutputInterface $output) {
		$output->writeln('');
		$output->writeln('<comment>Starting application "' . self::COMMAND_NAME . '"</comment>');
		$output->writeln('');
	}

	protected function outputEndMessage(OutputInterface $output) {
		$output->writeln('');
		$output->writeln('<comment>Application "' . self::COMMAND_NAME . '" finished</comment>');
		$output->writeln('');
	}
}