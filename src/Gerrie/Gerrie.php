<?php
/**
 * @todo more documentation, license and so on ...
 *
 * @todo further improvement / data mining
 * This Gerrit exporter got lot of potentials.
 * At the moment only the current state of the Gerrit system will be exported / represented in our database.
 * But in the next run, we "only" update our data.
 * The "real" raw data won`t be saved.
 * e.g. During a new patchset, the commit message can be updated, but we only store the current commit message.
 * What about the previous commit message? Analysis like "what kind of changed did the community x apply during
 * the patchsets to the commit message" are not possible at the moment.
 * So this "history" "raw" data thingy might be a good canditdate for an amazing improvement.
 * Maybe you got some spare time, motivation and skill to do this? This would be cool :)
 * If you need more information, just contact me. Thanks :)
 *
 * @author Andy Grunwald <andygrunwald@gmail.com>
 */
namespace Gerrie;

use Gerrie\Helper\Database;
use Symfony\Component\Console\Output\OutputInterface;

class Gerrie {

	/**
	 * Base interface of PSR Logging
	 *
	 * @var string
	 */
	const PSR_LOG_BASE = 'Psr\Log\LoggerInterface';

	/**
	 * Base interface of Symfony console output
	 *
	 * @var string
	 */
	const SYMFONY_CONSOLE_BASE = 'Symfony\Component\Console\Output\OutputInterface';

	/**
	 * Database helper object
	 *
	 * @var \Gerrie\Helper\Database
	 */
	protected $database = null;

	/**
	 * Storage for data services.
	 *
	 * @var \Gerrie\DataService\Base
	 */
	protected $dataService = null;

	/**
	 * Config array
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Timer information
	 *
	 * @var array
	 */
	protected $timer = array();

	/**
	 * Statistics information
	 *
	 * @var array
	 */
	protected $statsContainer = array();

	/**
	 * @var \Symfony\Component\Console\Output\OutputInterface
	 */
	protected $output = null;

	/**
	 * Server ID for Gerrit database identification
	 *
	 * @var int
	 */
	protected $serverId = 0;

	/**
	 * If this is the first time that one Gerrit server is imported,
	 * some cases must not be executed.
	 *
	 * @var bool
	 */
	protected $serversFirstRun = false;

	/**
	 * Green output text
	 *
	 * @var integer
	 */
	const OUTPUT_INFO = 1;

	/**
	 * Yellow output text
	 *
	 * @var integer
	 */
	const OUTPUT_COMMENT = 2;

	/**
	 * Red output text
	 *
	 * @var integer
	 */
	const OUTPUT_ERROR = 3;

	/**
	 * Constructor
	 *
	 * @param \Gerrie\Helper\Database $database The database helper object
	 * @param \Gerrie\DataService\Base $dataService The data service object
	 * @param array $config The configuration array
	 * @return void
	 */
	public function __construct(Database $database, \Gerrie\DataService\Base $dataService, array $config) {
		$this->setDatabase($database);
		$this->setDataService($dataService);
		$this->setConfig($config);
	}

	/**
	 * Sets the configuration
	 *
	 * @param array $config The configuration
	 */
	public function setConfig($config) {
		$this->config = $config;
	}

	/**
	 * Gets the configuration
	 *
	 * @return array
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * Sets the database object
	 *
	 * @param \Gerrie\Helper\Database $database The database object
	 */
	public function setDatabase($database) {
		$this->database = $database;
	}

	/**
	 * Gets the database object
	 *
	 * @return \Gerrie\Helper\Database
	 */
	public function getDatabase() {
		return $this->database;
	}

	/**
	 * Sets the data service
	 *
	 * @param \Gerrie\DataService\Base $dataService Data service object
	 * @return void
	 */
	public function setDataService(\Gerrie\DataService\Base $dataService) {
		$this->dataService = $dataService;
	}

	/**
	 * Gets a data service
	 *
	 * @return \Gerrie\DataService\Base
	 */
	public function getDataService() {
		return $this->dataService;
	}

	/**
	 * Sets the output object for CLI output
	 *
	 * @param \Symfony\Component\Console\Output\OutputInterface|\Monolog\Logger $output The output object
	 * @return void
	 */
	public function setOutput($output) {
		$psrLogBase = self::PSR_LOG_BASE;
		$consoleBase = self::SYMFONY_CONSOLE_BASE;

		if (($output instanceof $psrLogBase) === false && ($output instanceof $consoleBase) === false) {
			$className = get_class($output);
			$message = 'Output class "%s" not supported. Only "%s" or "%s"';
			throw new \Exception(sprintf($message, $className, $psrLogBase, $consoleBase), 1369595109);
		}

		$this->output = $output;
	}

	/**
	 * Gets the output object for CLI output
	 *
	 * @return \Symfony\Component\Console\Output\OutputInterface|null
	 */
	public function getOutput() {
		return $this->output;
	}

	/**
	 * Sets the serverFirstRun value :)
	 *
	 * @param bool $firstRun True = yes, false otherwise
	 * @return void
	 */
	public function setServersFirstRun($firstRun = true) {
		$this->serversFirstRun = (bool) $firstRun;
	}

	/**
	 * Checks if this run is the first run of this Gerrit server
	 *
	 * @return bool
	 */
	public function isServersFirstRun() {
		return $this->serversFirstRun;
	}

	/**
	 * Output a given $message, if $this->output is set with an OutputInterface object
	 *
	 * @param string $message The message to output
	 * @param int $type The color of output
	 * @return void
	 */
	protected function output($message, $type = self::OUTPUT_INFO) {
		$output = $this->getOutput();

		if ($output === null) {
			return;
		}

		// Chose color type of message
		switch($type) {
			case self::OUTPUT_COMMENT:
				$logMethod = 'info';
				$prefix = '<comment>';
				$postfix = '</comment>';
				break;

			case self::OUTPUT_ERROR:
				$logMethod = 'critical';
				$prefix = '<error>';
				$postfix = '</error>';
				break;

			case self::OUTPUT_INFO:
			default:
				$logMethod = 'info';
				$prefix = '<info>';
				$postfix = '</info>';
				break;
		}

		$psrLogBase = self::PSR_LOG_BASE;
		$consoleBase = self::SYMFONY_CONSOLE_BASE;

		if ($output instanceof $psrLogBase) {
			$output->$logMethod($prefix . $message . $postfix);

		} else if ($output instanceof $consoleBase) {
			$output->writeln($prefix . $message . $postfix);
		}

	}

	/**
	 * Saves a time next to a token.
	 * This can be use to measure import tasks.
	 *
	 * @param string $token A free chosen token. e.g. starttime, endtime, ...
	 * @param int $time A timestamp or another time identifier
	 * @return void
	 */
	protected function setTime($token, $time = 0) {
		if ($time == 0) {
			$time = time();
		}

		$this->timer[$token] = $time;
	}

	/**
	 * Returns the saved time identifier for given $token.
	 *
	 * @param string $token A free chosen token. e.g. starttime, endtime, ...
	 * @return int
	 */
	protected function getTime($token) {
		return $this->timer[$token];
	}

	/**
	 * Saves a statistic next to a token.
	 * This can be use to collect numbers about actions (e.g. inserts, updates, ...)
	 *
	 * @param string $token Name of statistic
	 * @param int $count Value of statistic
	 * @return void
	 */
	protected function setStatistic($token, $count) {
		$this->statsContainer[$token] = $count;
	}

	/**
	 * Returns the saved statistic value for a given $token
	 *
	 * @param string $token Name of statistic
	 * @return int
	 */
	protected function getStatistic($token) {
		return $this->statsContainer[$token];
	}

	/**
	 * Outputs the current memory usage
	 *
	 * @see http://de3.php.net/manual/de/function.memory-get-usage.php#96280
	 *
	 * @return void
	 */
	protected function outputMemoryUsage() {
		$memory = memory_get_usage(true);

		$unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		$memory = round($memory / pow(1024, ($i = floor(log($memory, 1024)))), 2) . ' ' . $unit[$i];

		$this->output('Memory usage: ' . $memory, self::OUTPUT_COMMENT);
	}

	/**
	 * Outputs some statistics about the import run.
	 * e.g. Needed time, memory, etc.
	 *
	 * @return void
	 */
	protected function outputEndStatistics() {
		$runtime = $this->getTime('end') - $this->getTime('start');

		$this->output('');
		$this->output('Finish', self::OUTPUT_COMMENT);
		$this->output('Runtime: ' . number_format(($runtime / 60), 2, ',', '.') . ' Min. (' . $runtime . ' seconds)', self::OUTPUT_COMMENT);
		$this->outputMemoryUsage();
		$this->output('');
	}

	/**
	 * Sets the Gerrit system server id
	 *
	 * @param int $serverId Gerrit server id from the database
	 * @return void
	 */
	public function setServerId($serverId) {
		$this->serverId = $serverId;
	}

	/**
	 * Returns the Gerrit system server id
	 *
	 * @return int
	 */
	public function getServerId() {
		return $this->serverId;
	}

	/**
	 * Queries all projects from the given $host and insert them to our database.
	 *
	 * @param string $host Gerrit host
	 * @return void
	 * @throws \Exception
	 */
	protected function proceedProjects($host) {
		$this->outputHeadline('Proceed Projects');

		$projects = $this->dataService->getProjects();

		if (is_array($projects) === false) {
			throw new \Exception('No projects found on "' . $host . '"!', 1363894633);
		}

		$this->importProjects($projects);
	}

	/**
	 * Entry point for external source code.
	 * This method starts to export the data from a whole Gerrit review system
	 *
	 * @return bool
	 */
	public function export() {
		$this->setTime('start');

		$host = $this->getDataService()->getHost();

		// Here we go. Lets get the export party started.
		// At first, lets check if the current Gerrit review system is known by the database
		$this->proceedServer($this->config['Name'], $host);

		// After this, lets start to save all projects.
		// We need the projects first, because this is our "entry point" for the data mining.
		$this->proceedProjects($host);

		$this->output('');
		$this->outputHeadline('Export changesets per project');

		// Get all projects again and loop over every single project to import them :)
		$projects = $this->getGerritProjectsByServerId($this->getServerId());
		foreach($projects as $project) {
			$this->output('Project "' . $project['name'] . '" ... Starts', self::OUTPUT_COMMENT);

			$this->proceedChangesetsOfProject($host, $project);

			$this->output('Project "' . $project['name'] . '" ... Ends', self::OUTPUT_COMMENT);
			$this->outputMemoryUsage();
			$this->output('');
		}

		// Clear the temp tables. The data is not needed anymore
		//$this->cleanupTempTables();

		$this->setTime('end');
		$this->outputEndStatistics();

		return true;
	}

	/**
	 * Imports all changes from a incoming project.
	 *
	 * It queries the Gerrit system via SSH API with the configured limit.
	 * It receives  all changesets, patchsets, approvals and comments automatically and store them in our database.
	 *
	 * @see https://review.typo3.org/Documentation/cmd-query.html
	 *
	 * @param string $host Gerrit server host
	 * @param array $project The current project
	 * @return void
	 */
	public function proceedChangesetsOfProject($host, array $project) {

		// Clear the temp tables first
		$this->cleanupTempTables();

		// Query the data till we receive all data
		$queryRun = $startNum = $endNum = 0;
		$sortKey = null;
		$changeSetQueryLimit = $this->getDataService()->getQueryLimit();

		do {
			$startNum = $queryRun * $changeSetQueryLimit;
			$endNum = $startNum + $changeSetQueryLimit;
			$this->output('Querying ' . $host . ' via SSH for changes ' . $startNum . '...' . $endNum);

			$data = $this->getDataService()->getChangesets($project['name'], $sortKey);
			$queryStatus = $this->transferJsonToArray(array_pop($data));

			$this->output('Received ' . $queryStatus['rowCount'] . ' changes to process');

			// One project can get n changesets. So lets do the same as one level above (the project loop)
			// Loop over all changesets and proceed (import / update) them ;)
			foreach($data as $singleChangeset) {
				$changeSet = $this->transferJsonToArray($singleChangeset);

				$this->output($changeSet['subject']);

				// Import this changeset :)
				$this->proceedChangeset($changeSet, $project);
			}

			// First we query Gerrit for rows. If there are more rows than this limit,
			// we resume at the next changeset with the resume_sortkey feature.
			// But if the row cound lower the changeset query limit, we can exit here.
			// There are no more changesets in the next ssh query
			if ($queryStatus['rowCount'] > 0 && $queryStatus['rowCount'] < $changeSetQueryLimit) {
				break;
			}

			// We need to determine the last sortkey to continue the data reading for the next command.
			// How does it work? See the API documentation of Gerrit mentioned in the doc block
			$sortKey = $this->getLastSortKey($data);

			$queryRun++;
		} while ($queryStatus['rowCount'] > 0);

		// Take care of 'dependsOn' relations in changesets
		$this->proceedChangeSetsDependsOnRelation();

		// Take care of 'neededBy' relations in changesets
		$this->proceedChangeSetsNeededByRelation();
	}

	/**
	 * Update method to map the 'neededBy' relation from the temp table
	 * to the correct changeset table.
	 *
	 * One changeset can have n neededBy relations
	 *
	 * @return void
	 */
	protected function proceedChangeSetsNeededByRelation() {
		$dbHandle = $this->database->getDatabaseConnection();

		$tmpTable = Database::TABLE_TMP_DEPENDS_NEEDED;
		$neededByTable = Database::TABLE_CHANGESET_NEEDEDBY;
		$changeSetTable = Database::TABLE_CHANGESET;
		$patchSetTable = Database::TABLE_PATCHSET;

		$query = '
			INSERT INTO ' . $neededByTable . ' (`changeset`, `needed_by`, `tstamp`, `crdate`)
			SELECT
				' . $changeSetTable . '.`id`,
				neededByChangeset.`id`,
				UNIX_TIMESTAMP(),
				UNIX_TIMESTAMP()
			FROM
				' . $tmpTable . '
				INNER JOIN ' . $changeSetTable . ' AS neededByChangeset ON (
					' . $tmpTable . '.`identifier` = neededByChangeset.`identifier`
					AND ' . $tmpTable . '.`number` = neededByChangeset.`number`
				)
				INNER JOIN gerrit_patchset ON (
					neededByChangeset.`id` = ' . $patchSetTable . '.`changeset`
					AND ' . $patchSetTable . '.`revision` = ' . $tmpTable . '.`revision`
					AND ' . $patchSetTable . '.`ref` = ' . $tmpTable . '.`ref`
				)
				INNER JOIN ' . $changeSetTable . ' ON (
					' . $changeSetTable . '.`id` = ' . $tmpTable . '.`changeset`
				)
			WHERE
				' . $tmpTable . '.`status` = ' . intval(Database::TMP_DEPENDS_NEEDED_STATUS_NEEDEDBY) . '
			GROUP BY
				' . $changeSetTable . '.`id`, neededByChangeset.`id`
			ON DUPLICATE KEY UPDATE
				' . $neededByTable . '.`tstamp` = UNIX_TIMESTAMP()';

		$dbHandle->exec($query);
	}

	/**
	 * Update method to map the 'dependsOn' relation from the temp table
	 * to the correct changeset table.
	 *
	 * One changeset can have one dependsOn relation.
	 *
	 * @see $this->proceedChangeSetsDependsOnAndNeededBy()
	 *
	 * @return void
	 */
	protected function proceedChangeSetsDependsOnRelation() {
		$dbHandle = $this->database->getDatabaseConnection();

		$tmpTable = Database::TABLE_TMP_DEPENDS_NEEDED;
		$changeSetTable = Database::TABLE_CHANGESET;
		$patchSetTable = Database::TABLE_PATCHSET;

		$query = '
			UPDATE
				' . $tmpTable . '
				INNER JOIN ' . $changeSetTable . ' AS dependsOnChangeset ON (
					' . $tmpTable . '.`identifier` = dependsOnChangeset.`identifier`
					AND ' . $tmpTable . '.`number` = dependsOnChangeset.`number`
				)
				INNER JOIN ' . $patchSetTable . ' ON (
					dependsOnChangeset.`id` = ' . $patchSetTable . '.`changeset`
					AND ' . $patchSetTable . '.`revision` = ' . $tmpTable . '.`revision`
					AND ' . $patchSetTable . '.`ref` = ' . $tmpTable . '.`ref`
				)
				INNER JOIN ' . $changeSetTable . ' ON (
					' . $changeSetTable . '.`id` = ' . $tmpTable . '.`changeset`
				)
			SET
				' . $changeSetTable . '.`depends_on` = dependsOnChangeset.`id`
			WHERE
				' . $tmpTable . '.`status` = ' . intval(Database::TMP_DEPENDS_NEEDED_STATUS_DEPENDSON);

		$dbHandle->exec($query);
	}

	/**
	 * Proceed tracking ids per changeset.
	 * Checks if they are already exists. If not, insert them.
	 *
	 * @param integer $changeSetId Current changeset id
	 * @param array $trackingIds Tracking ids to proceed
	 * @return void
	 */
	protected function proceedTrackingIds($changeSetId, array $trackingIds) {
		// The commit message includes the tracking ids.
		// Via a new patchset, the commit message (inkl. tracking ids) can change.
		// To handle this case, we set all tracking ids to referenced_earlier => 1
		// and proceed the tracking ids. If the tracking id is still in the commit message
		// it will be set back to referenced_earlier => 0.
		$dataToUpdate = array(
			'referenced_earlier' => 1
		);
		$where = 'changeset = ' . intval($changeSetId);
		$this->updateRecords(Database::TABLE_TRACKING_ID, $dataToUpdate, $where);

		foreach ($trackingIds as $trackingId) {
			$system = $this->proceedLookupTable(Database::TABLE_TRACKING_SYSTEM, 'id', 'name', $trackingId['system']);

			$trackingIdRow = $this->getGerritTrackingIdByIdentifier($changeSetId, $system, $trackingId['id']);

			// If there is no tracking id with the current changeset, system and number, insert it
			if ($trackingIdRow === false) {
				$trackingRow = array(
					'changeset' => $changeSetId,
					'system' => $system,
					'number' => $trackingId['id']
				);
				$this->insertRecord(Database::TABLE_TRACKING_ID, $trackingRow);

			// We know this tracking id, but this id was set to referenced_earlier => 1 earlier.
			// So lets reactivate this!
			} else {
				$dataToUpdate = array(
					'referenced_earlier' => 0
				);
				$this->updateRecord(Database::TABLE_TRACKING_ID, $dataToUpdate, $trackingIdRow['id']);
			}

			$trackingId = $this->unsetKeys($trackingId, array('id', 'system'));
			$this->checkIfAllValuesWereProceeded($trackingId, 'Tracking ID');
		}
	}

	/**
	 * Returns a tracking id by the unique identifier
	 *
	 * @param int $changeSetId ID of a record from the changeset table
	 * @param int $system ID of a record from the system table
	 * @param string $trackingId Tracking id
	 * @return mixed
	 */
	protected function getGerritTrackingIdByIdentifier($changeSetId, $system, $trackingId) {
		$dbHandle = $this->database->getDatabaseConnection();

		$query = 'SELECT `id`, `changeset`, `system`, `number`
				  FROM ' . Database::TABLE_TRACKING_ID . '
				  WHERE `changeset` = :changeset
						AND `system` = :system
						AND `number` = :number';
		$statement = $dbHandle->prepare($query);

		$statement->bindParam(':changeset', $changeSetId, \PDO::PARAM_INT);
		$statement->bindParam(':system', $system, \PDO::PARAM_INT);
		$statement->bindParam(':number', $trackingId, \PDO::PARAM_STR);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Returns the unique identifier for a lookup table value.
	 * If the requested lookup table value ($compareValue) not available,
	 * it will be inserted
	 *
	 * @param string $table Lookup table
	 * @param string $idField Field name of unique identifier (most single primary key)
	 * @param $compareField Compare field. e.g. 'name' in branch table
	 * @param $compareValue Compare value. e.g. 'master' in branch table
	 * @return int
	 */
	protected function proceedLookupTable($table, $idField, $compareField, $compareValue) {
		$whereParts = array($compareField => $compareValue);
		$row = $this->getLookupTableValues($table, array($idField), $whereParts);

		// If there is no branch with the current branch name, insert it
		if ($row === false) {
			$idValue = $this->insertRecord($table, array($compareField => $compareValue));

		} else {
			$idValue = $row[$idField];
		}

		return $idValue;
	}

	/**
	 * Proceeds a single changeset.
	 * One changeset can have n patchsets, n comments and so on.
	 *
	 * @param array $changeSet The current changeset
	 * @param array $project The current project
	 * @return void
	 */
	protected function proceedChangeset(array $changeSet, array $project) {

		// Take care of branch
		$changeSet['branch'] = $this->proceedLookupTable(Database::TABLE_BRANCH, 'id', 'name', $changeSet['branch']);

		// Take care of owner
		$changeSet['owner'] = $this->proceedPerson($changeSet['owner']);

		// Take care of status
		$changeSet['status'] = $this->proceedLookupTable(Database::TABLE_STATUS, 'id', 'name', $changeSet['status']);

		$changeSetData = array(
			'project' => $project['id'],
			'branch' => $changeSet['branch'],
			'topic' => ((isset($changeSet['topic']) === true) ? $changeSet['topic']: ''),
			'identifier' => $changeSet['id'],
			'number' => $changeSet['number'],
			'subject' => $changeSet['subject'],
			'owner' => $changeSet['owner']['id'],
			'url' => $changeSet['url'],
			'commit_message' => $changeSet['commitMessage'],
			'created_on' => $changeSet['createdOn'],
			'last_updated' => $changeSet['lastUpdated'],
			'sort_key' => $changeSet['sortKey'],
			'open' => intval($changeSet['open']),
			'status' => $changeSet['status'],
		);

		// A changeset don`t have a unique identifier.
		// The Gerrit documentation says that "<project>~<branch>~<Change-Id>" will be enough
		// @see https://gerrit-review.googlesource.com/Documentation/rest-api-changes.html#change-id
		// This combination seems to be "unique enough", BUT we have to add createdOn property as well.
		// Why? Have a look at these two changesets ...
		// e.g. https://review.typo3.org/#/c/1427/ and https://review.typo3.org/#/c/1423/
		// If anyone got an answer, why this changes got the same change id, the same branch and the same project
		// Let me know.
		// Check if this changeset already exists ...
		$changeSetRow = $this->getGerritChangesetByIdentifier($project['id'], $changeSet['branch'], $changeSet['id'], $changeSet['createdOn']);
		if ($changeSetRow === false) {
			$changeSet['id'] = $this->insertRecord(Database::TABLE_CHANGESET, $changeSetData);

			$this->output('=> Inserted (ID: ' . $changeSet['id'] . ')');

		// If the timestamp 'last updated' is newer than our database timestamp
		// There must something new ... update it! e.g.
		// If there is a new comment, lastUpdated will be updated with a new timestamp
		// If there is a new patchset, lastUpdated will be updated with a new timestamp ...
		// If a changeset is merged, it is not possible to push a new patch set
		} elseif ($changeSet['lastUpdated'] > $changeSetRow['last_updated']) {

			$this->checkIfServersFirstRun('Changeset', 1363893102, array($changeSet, $changeSetRow));

			// Calculate the difference and update it :)
			$changeSet['id'] = $changeSetRow['id'];
			$dataDiff = array_diff($changeSetData, $changeSetRow);
			$this->updateRecord(Database::TABLE_CHANGESET, $dataDiff, $changeSet['id']);

			$this->output('=> Updated (ID: ' . $changeSet['id'] . ')');

		// If the timestamp 'last updated' is equal than our database timestamp
		// There are no new information for us ... stop proceeding this changeset.
		} elseif ($changeSet['lastUpdated'] == $changeSetRow['last_updated']) {

			$this->output('=> Nothing new. Skip it');
			return;
		}

		// Unset not needed keys, because a) memory and b) to check if there is ata which was not imported :)
		$keysToUnset = array(
			'project', 'branch', 'topic', 'identifier', 'number', 'subject', 'owner', 'url',
			'commitMessage', 'createdOn', 'lastUpdated', 'sortKey', 'open', 'status'
		);
		$changeSet = $this->unsetKeys($changeSet, $keysToUnset);

		// Take care of tracking ids
		if (isset($changeSet['trackingIds']) === true) {
			$this->proceedTrackingIds($changeSet['id'], $changeSet['trackingIds']);
			$changeSet = $this->unsetKeys($changeSet, array('trackingIds'));
		}

		// One changeset can have n patchsets. So do the same as the level above
		// Loop over the patchsets and import one for one
		foreach($changeSet['patchSets'] as $patchSet) {
			$this->proceedPatchset($patchSet, $changeSet);
		}
		$changeSet = $this->unsetKeys($changeSet, array('patchSets'));

		// Take care of current patch set
		$currentPatchSetId = ((isset($changeSetRow['current_patchset']) === true) ? $changeSetRow['current_patchset']: 0);
		$this->proceedCurrentPatchSet($changeSet, $currentPatchSetId);
		$changeSet = $this->unsetKeys($changeSet, array('currentPatchSet'));

		// Take care of "dependsOn" and "neededBy"
		$this->proceedChangeSetsDependsOnAndNeededBy($changeSet);
		$changeSet = $this->unsetKeys($changeSet, array('dependsOn', 'neededBy'));

		// Take care of "submitRecords"
		$this->proceedSubmitRecords($changeSet);
		unset($changeSet['submitRecords']);

		// Sometimes a changeSet does not get any comments.
		// In this case, the comments key does not exist and we can skip it.
		if (isset($changeSet['comments']) === true) {
			foreach($changeSet['comments'] as $key => $comment) {
				// Yep, i know. To trust the order of comments from an external API (Gerrit) is not very common.
				// But we need the "key" to save "duplicate" comments as well.
				// Otherwise we do not get all data.
				// e.g. https://review.typo3.org/#/c/564/
				// In this case Robert Lemke posted the same comment in the same second twice.
				// And we want this data as well, to get correct statistics.
				// We calculate +1 to the key, because in an array, php starts with 0
				// but in human thinnking, all stuff starts with 1.
				// So the first comment won`t be comment #0, it will be comment #1
				$this->proceedComment($comment, $changeSet, ($key + 1));
			}
			$changeSet = $this->unsetKeys($changeSet, array('comments'));
		}

		$changeSet = $this->unsetKeys($changeSet, array('id'));

		$this->checkIfAllValuesWereProceeded($changeSet, 'Change set');

		unset($changeSet);
	}

	/**
	 * Proceeds the 'submitRecords' key of a changeset
	 *
	 * @todo implement this!
	 *
	 * @param array $changeSet Current changeset
	 * @return void
	 */
	protected function proceedSubmitRecords(array $changeSet) {
		#print_r(($changeSet['submitRecords']));
		#print_r($changeSet);
		#die();

		if (is_array($changeSet['submitRecords'] === false)) {
			return;
		}

		foreach ($changeSet['submitRecords'] as $submitRecord) {
			$submitRecordData = array(
				'changeset' => $changeSet['id'],
				'status' => $submitRecord['status'],
			);
			$wherePart = array('changeset' => $changeSet['id']);
			$submitRecordRow = $this->getLookupTableValues(Database::TABLE_SUBMIT_RECORDS, array('id'), $wherePart);
			if ($submitRecordRow === false) {
				$submitRecordData = array(
					'changeset' => $changeSet['id'],
					'status' => $submitRecord['status'],
				);
				$id = $this->insertRecord(Database::TABLE_SUBMIT_RECORDS, $submitRecordData);

			} else {
				$id = $submitRecordRow['id'];
			}

			$database = $this->getDatabase()->getDatabaseConnection();
			$labelTable = Database::TABLE_SUBMIT_RECORD_LABELS;
			$query = '
				INSERT INTO `' . $labelTable . '` (`submit_record`, `label`, `status`, `tstamp`, `crdate`)
				VALUES (:submit_record, :label, :status, :tstamp, :crdate)
				ON DUPLICATE KEY UPDATE
					' . $labelTable . '.`status` = :status,
					' . $labelTable . '.`tstamp` = :tstamp';

			$labels = $submitRecord['labels'];
			foreach ($labels as $labelInfo) {
				/*
				// @todo hier weitermachen!!!!
				$dbHandle = $this->getDatabase()->getDatabaseConnection();
				$statement = $dbHandle->prepare($query);

				$statement->bindParam(':value', $value, \PDO::PARAM_STR);
				$executeResult = $statement->execute();

				$this->database->checkQueryError($statement, $executeResult);
				*/
			}

			/*
			 array(2) {
					'status' => string(9) "NOT_READY"
					'labels' => array(2) {
						[0] => array(2) {
							'label' => string(8) "Verified"
							'status' => string(4) "NEED"
						}
						[1] => array(2) {
							'label' => string(11) "Code-Review"
							'status' => string(4) "NEED"
						}
					}
				}
			*/
		}

	}

	/**
	 * Proceed "dependsOn" and "neededBy" for a single changeset.
	 * This is a little bit tricky, because there can be a dependsOn
	 * reference to a changeset which is not imported yet.
	 *
	 * Solution: A temp table
	 * We insert all "dependsOn" and "neededBy" records into one temp table.
	 * After we finished all changesets for this project, we got all changesets
	 * for our "dependsOn" and "neededBy" mapping, too.
	 * Then we update the changesets table :)
	 *
	 * "dependsOn" and "neededBy" ... WHAT?!
	 * If you don`t know what this is, have a look at https://review.typo3.org/#/c/17997/ into the dependency section
	 *
	 * @see $this->proceedChangeSetsDependsOnRelation()
	 *
	 * @param array $changeSet
	 * @return void
	 */
	protected function proceedChangeSetsDependsOnAndNeededBy(array $changeSet) {
		$keysToUnset = array('id', 'number', 'revision', 'ref');

		// Take care of neededBy
		if (isset($changeSet['neededBy']) === true) {
			foreach ($changeSet['neededBy'] as $neededBy) {
				$neededByData = array(
					'changeset' => $changeSet['id'],
					'identifier' => $neededBy['id'],
					'number' => $neededBy['number'],
					'revision' => $neededBy['revision'],
					'ref' => $neededBy['ref'],
					'status' => Database::TMP_DEPENDS_NEEDED_STATUS_NEEDEDBY
				);
				$this->insertRecord(Database::TABLE_TMP_DEPENDS_NEEDED, $neededByData);

				$neededBy = $this->unsetKeys($neededBy, $keysToUnset);
				$this->checkIfAllValuesWereProceeded($neededBy, 'neededBy');
			}

			unset($neededBy);
		}

		// Take care of dependsOn
		if (isset($changeSet['dependsOn']) === true) {

			$keysToUnset[] = 'isCurrentPatchSet';

			foreach ($changeSet['dependsOn'] as $dependsOn) {
				$dependsOnData = array(
					'changeset' => $changeSet['id'],
					'identifier' => $dependsOn['id'],
					'number' => $dependsOn['number'],
					'revision' => $dependsOn['revision'],
					'ref' => $dependsOn['ref'],
					'is_current_patchset' => $dependsOn['isCurrentPatchSet'],
					'status' => Database::TMP_DEPENDS_NEEDED_STATUS_DEPENDSON
				);
				$this->insertRecord(Database::TABLE_TMP_DEPENDS_NEEDED, $dependsOnData);

				$dependsOn = $this->unsetKeys($dependsOn, $keysToUnset);
				$this->checkIfAllValuesWereProceeded($dependsOn, 'dependsOn');
			}

			unset($dependsOn);
		}
	}

	/**
	 * Updates the current patch set for this changeset if necessary.
	 *
	 * @param array $changeSet The changeSet from Gerrit
	 * @param int $currentPatchSetId The current patchset id from our database
	 * @return void
	 */
	protected function proceedCurrentPatchSet(array $changeSet, $currentPatchSetId) {
		$currentPatchSet = $changeSet['currentPatchSet'];
		$patchSetRow = $this->getGerritPatchsetByIdentifier($changeSet['id'], $currentPatchSet['number'], $currentPatchSet['revision'], $currentPatchSet['createdOn']);

		// If the current currentPatchSet in database not equal the patchset from Gerrit, update it
		if ($patchSetRow['id'] != $currentPatchSetId) {
			$updateData = array('current_patchset' => $patchSetRow['id']);
			$this->updateRecord(Database::TABLE_CHANGESET, $updateData, $changeSet['id']);
		}
	}

	/**
	 * Unsets an amount of keys in goven $data
	 *
	 * @param array $data Data array where the keys will be unset
	 * @param array $keyList List of keys which will be unset
	 * @return array
	 */
	public function unsetKeys(array $data, array $keyList) {
		foreach ($keyList as $key) {
			if (isset($data[$key]) === true) {
				unset($data[$key]);
			}
		}

		return $data;
	}

	/**
	 * Proceeds a single comment.
	 *
	 * Comments are not updateable. So there is no need to provide an update mechanism here.
	 *
	 * @param array $comment The current comment
	 * @param array $changeSet The current changeset
	 * @param integer $key The $key value of the foreach loop of comments (order number)
	 * @return void
	 */
	protected function proceedComment(array $comment, array $changeSet, $key) {
		$reviewer = $this->proceedPerson($comment['reviewer']);

		// A comment don`t have a unique identifier, so we must generate an ID on our own.
		// Changeset Id + Reviewer ID + Key + Timestamp. This combination is "unique enough".
		// Is this comment already in database?
		$commentRecord = $this->getGerritCommentByIdentifier($changeSet['id'], $reviewer['id'], $comment['timestamp'], $key);
		if ($commentRecord === false) {
			$commentData = array(
				'changeset' => $changeSet['id'],
				'timestamp' => $comment['timestamp'],
				'reviewer' => $reviewer['id'],
				'message' => $comment['message'],
				'number' => $key
			);
			$this->insertRecord(Database::TABLE_COMMENT, $commentData);

			$comment = $this->unsetKeys($comment, array('timestamp', 'reviewer', 'message'));
			$this->checkIfAllValuesWereProceeded($comment, 'Comment');
		}
	}

	/**
	 * Proceeds a single patchset.
	 * One patchset can have n approvals, comments and so on.
	 *
	 * @param array $patchset The current patchset
	 * @param array $changeSet The current changeset
	 * @return void
	 */
	protected function proceedPatchset(array $patchset, array $changeSet) {

		// A patchset don`t have a unique identifier, so have to combine different fields
		// (revision is not unique in Gerrit): ChangeSet-Id + Patchset number + Patchset revision + Patchset created on
		// This combination is "unique enough".
		// We have to include the number here, because in some cases, the revision and created on timestamp is the same
		// e.g. http://review.typo3.org/9221
		// Is this patchset already in database?
		$patchSetRow = $this->getGerritPatchsetByIdentifier($changeSet['id'], $patchset['number'], $patchset['revision'], $patchset['createdOn']);
		if($patchSetRow === false) {
			$uploader = $this->proceedPerson($patchset['uploader']);
			$patchSetData = array(
				'changeset' => $changeSet['id'],
				'number' => $patchset['number'],
				'revision' => $patchset['revision'],
				'ref' => $patchset['ref'],
				'uploader' => $uploader['id'],
				'created_on' => $patchset['createdOn'],
			);
			$patchset['id'] = $this->insertRecord(Database::TABLE_PATCHSET, $patchSetData);

			// Import files per patchset
			$this->proceedFiles($patchset);

		// A pushed patchset can`t be updated
		// So, we do not have to provide an update mechanism for patchsets.
		} else {

			$this->checkIfServersFirstRun('Patchset', 1363893175, array($patchset, $patchSetRow));
			$patchset['id'] = $patchSetRow['id'];
		}

		$patchset = $this->unsetKeys($patchset, array('files'));

		// Unset not needed keys, because a) memory and b) to check if there are keys which were not imported :)
		$patchset = $this->unsetKeys($patchset, array('number', 'revision', 'ref', 'uploader', 'createdOn'));

		// We need to set all approvals for this patchset as 'voted_earlier' => 1
		// Because a user can vote both values (Code Review and Verified).
		// After some time, the user can set one value e.g. Code Review back to value 0
		// In this case we do not get the Code Review back from API
		// If we do not set all approvals to 'voted_earlier' => 1
		// we store approvals which are not active anymore
		$updateData = array('voted_earlier' => 1);
		$where = '`patchset` = ' . intval($patchset['id']);
		$this->updateRecords(Database::TABLE_APPROVAL, $updateData, $where);

		// Sometimes a patchset does not get any approval.
		// In this case, the approvals key does not exist and we can skip it.
		// e.g. https://review.typo3.org/#/c/16924/
		if (isset($patchset['approvals']) === true) {
			// One patchset can have n approvals. So do the same as the level above
			// Loop over the approvals and import one for one
			foreach($patchset['approvals'] as $approval) {
				$this->proceedApproval($approval, $patchset);
			}

			$patchset = $this->unsetKeys($patchset, array('approvals'));
		}

		// @todo implement 'parents'
		$patchset = $this->unsetKeys($patchset, array('parents'));

		// Take care of comments in files
		if (isset($patchset['comments']) === true) {
			//$this->proceedFileComments($patchset); @todo comment and implement!!!
			$patchset = $this->unsetKeys($patchset, array('comments'));
		}

		$patchset = $this->unsetKeys($patchset, array('id'));

		$this->checkIfAllValuesWereProceeded($patchset, 'Patch set');

		unset($patchset);
	}

	// @todo comment + whole method
	protected function proceedFileComments(array $patchset) {

		// @todo maybe we need to add the deleted stuff here, too?
		// @todo what about "Done" comments?

		foreach ($patchset['comments'] as $comment) {
			// Take care of reviewer
			$comment['reviewer'] = $this->proceedPerson($comment['reviewer']);

			// Search the file id
			$whereParts = array(
				'patchset' => $patchset['id'],
				'file' => $comment['file']
			);
			$fileRow = $this->getLookupTableValues(Database::TABLE_FILES, array('id'), $whereParts);

			$commentRow = array(
				'patchset' => $patchset['id'],
				'file' => $fileRow['id'],
				'line' => $comment['line'],
				'reviewer' => $comment['reviewer']['id'],
				'message' => $comment['message'],
			);

			// Attention: One person can comment twice at the same line.
			// We do not get any timestamp :(
			// e.g. see https://review.typo3.org/#/c/11758/1/ExtendingSphinxForTYPO3/build/.gitignore,unified
			// @todo fix it. how?
			$fileCommentRow = $this->getGerritFileCommentByIdentifier($patchset['id'], $fileRow['id'], $comment['line'], $comment['reviewer']['id']);
			if ($fileCommentRow === false) {
				$this->insertRecord(Database::TABLE_FILE_COMMENTS, $commentRow);

			} else {
				$this->checkIfServersFirstRun('File comment', 1363903827, array($commentRow, $fileCommentRow));

				$this->updateRecord(Database::TABLE_FILE_COMMENTS, $comment, $fileCommentRow['id']);
			}

			$comment = $this->unsetKeys($comment, array('file', 'line', 'reviewer', 'message'));

			$this->checkIfAllValuesWereProceeded($comment, 'File comment');
		}
	}

	/**
	 * Method to check if all data were imported.
	 * Normally, this export script unsets the exported value after proceeding.
	 *
	 * If there are values left in the array, there could be
	 * a) a bug, because the value is not unsetted
	 * b) a change in the Gerrit server API
	 * c) a bug, because not all values are exported / proceeded
	 *
	 * This methods help to detect this :)
	 *
	 * @param array $data Data to inspect
	 * @param string $level Level of data. e.g. patchset, comment, etc.
	 * @throws \Exception
	 */
	protected function checkIfAllValuesWereProceeded(array $data, $level) {
		if (count($data) > 0) {
			var_dump($data);
			throw new \Exception('Not all values were proceeded / exported. Please have a look at "' . $level . '"', 1363894644);
		}
	}

	/**
	 * Imports files per patchset
	 *
	 * @param array $patchset The current patchset
	 * @return void
	 */
	protected function proceedFiles(array $patchset) {
		foreach ($patchset['files'] as $file) {
			// Take care of file action
			$type = $this->proceedLookupTable(Database::TABLE_FILEACTION, 'id', 'name', $file['type']);

			$fileData = array(
				'patchset' => $patchset['id'],
				'file' => $file['file'],
				'type' => $type
			);
			$this->insertRecord(Database::TABLE_FILES, $fileData);

			$file = $this->unsetKeys($file, array('file', 'type'));
			$this->checkIfAllValuesWereProceeded($file, 'File');
		}
	}

	/**
	 * Will proceed a single approval.
	 *
	 * @param array $approval The current approval
	 * @param array $patchset The current patchset
	 * @return void
	 */
	protected function proceedApproval(array $approval, array $patchset) {
		$by = $this->proceedPerson($approval['by']);

		$approvalData = array(
			'patchset' => $patchset['id'],
			'type' => $approval['type'],
			'description' => ((isset($approval['description']) === true) ? $approval['description']: ''),
			'value' => $approval['value'],
			'granted_on' => $approval['grantedOn'],
			'by' => $by['id'],
			'voted_earlier' => 0
		);

		// An approval don`t have a unique identifier, so we must generate one on our own.
		// Patch-ID, type and user. This combination is "unique enough".
		// Is this approval already in database?
		$approvalRow = $this->getGerritApprovalByIdentifier($patchset['id'], $approval['type'], $by['id']);
		if($approvalRow === false) {
			$this->insertRecord(Database::TABLE_APPROVAL, $approvalData);

		// We know this approval. Just update it!
		} else {
			$this->checkIfServersFirstRun('Approval', 1363897318, array($approval, $approvalRow));

			$this->updateRecord(Database::TABLE_APPROVAL, $approvalData, $approvalRow['id']);
		}

		$approval = $this->unsetKeys($approval, array('type', 'description', 'value', 'grantedOn', 'by'));
		$this->checkIfAllValuesWereProceeded($approval, 'Approval');

		unset($approval);
	}

	/**
	 * Handles the import or update process of a single person.
	 * A person can be determined by name, an e-mail-address or by username.
	 *
	 * @param array $person The current person
	 * @return int ID of person
	 */
	protected function proceedPerson(array $person) {

		// If "Gerrit Code Review" posted some problems, e.g. path conflicts (e.g. https://review.typo3.org/#/c/4553/)
		// there is no person information.
		// Set it here, because otherwise we got empty persons ;)
		if(!$person['name'] && !$person['email'] && !$person['username']) {
			$person['name'] = 'Unknown (Exporter)';
			$person['email'] = 'mail@example.org';
			$person['username'] = 'Unknown_export_username';
		}

		// Sometimes you got an action by "Gerrit Code Review".
		// This "system user" does not have a username. Sad, isn`t ?
		// We got a present for him / her. A default username :)
		// e.g. https://review.typo3.org/#/c/4553/
		if ($person['name'] === 'Gerrit Code Review') {
			$person['username'] = 'Gerrit';
		}

		$emailPerson = $this->getPersonBy('email', $person['email']);
		if($emailPerson === false) {
			$usernamePerson = $this->getPersonBy('username', $person['username']);

			// If a person does not exist, create a new one.
			if($usernamePerson === false) {
				$personData = array(
					'name' => $person['name'],
					'username' => $person['username']
				);
				$person['id'] = $this->insertRecord(Database::TABLE_PERSON, $personData);

				$emailData = array(
					'person' => $person['id'],
					'email' => $person['email']
				);
				$this->insertRecord(Database::TABLE_EMAIL, $emailData);

				// Person exists, but has a new e-mail. Just add the e-mail-address to this person
			} else {
				$emailData = array(
					'person' => $usernamePerson['id'],
					'email' => $person['email']
				);
				$this->insertRecord(Database::TABLE_EMAIL, $emailData);
			}
		} else {
			$person['id'] = $emailPerson['id'];
		}

		return $person;
	}

	/**
	 * Determines the last SortKey of the Gerrit System.
	 * This is necessary to get a "pointer" to continue the data mining.
	 * Because it is not common to get ALL data with ONE request.
	 * a) There is a "limit" configured in Gerrit
	 * b) The memory of your server / computer is limited, too ;)
	 *
	 * @see https://review.typo3.org/Documentation/cmd-query.html
	 *
	 * @param array $data changeset data whoch was queried by Gerrit earlier
	 * @return string
	 */
	protected function getLastSortKey(array $data) {
		$lastChangeSet = $this->transferJsonToArray(array_pop($data));

		return $lastChangeSet['sortKey'];
	}

	/**
	 * Returns a person by $mode (email or username)
	 *
	 * @param string $mode 'email' or 'username'
	 * @param string $value An email adress or an username
	 * @return mixed
	 * @throws \Exception
	 */
	protected function getPersonBy($mode, $value) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		switch ($mode) {
			case 'email':
				$query = '
				SELECT
					person.`id`,
					person.`name`,
					person.`username`,
					email.`email`
				  FROM ' . Database::TABLE_EMAIL . ' email
				  INNER JOIN ' . Database::TABLE_PERSON . ' person ON (
					email.`person` = person.`id`
				  )
				  WHERE email.`email` = :value';
				break;
			case 'username':
				$query = 'SELECT `id`, `name`, `username`
				  FROM ' . Database::TABLE_PERSON . '
				  WHERE `username` = :value';
				break;
			default:
				throw new \Exception('Wrong mode selected!', 1363897547);
		}

		$statement = $dbHandle->prepare($query);

		$statement->bindParam(':value', $value, \PDO::PARAM_STR);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Returns all projects by server id
	 *
	 * @param int $serverId Server if of Gerrit server
	 * @return array
	 */
	protected function getGerritProjectsByServerId($serverId) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$query = 'SELECT `id`, `name` FROM ' . Database::TABLE_PROJECT . '
				  WHERE `server_id` = :server_id';
		$statement = $dbHandle->prepare($query);

		$statement->bindParam(':server_id', $serverId, \PDO::PARAM_INT);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Returns all projects from our database for given server id with given names.
	 *
	 * @param int $serverId Gerrit server id
	 * @param array $names Names who are looking for
	 * @return array
	 */
	protected function getGerritProjectsByName($serverId, array $names) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();
		$nameList = implode(',', $names);

		$query = 'SELECT `id`, `name` FROM ' . Database::TABLE_PROJECT . '
				  WHERE `server_id` = :server_id AND FIND_IN_SET(`name`, :names) > 0';
		$statement = $dbHandle->prepare($query);

		$statement->bindParam(':server_id', $serverId, \PDO::PARAM_INT);
		$statement->bindParam(':names', $nameList, \PDO::PARAM_STR);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Return a single project from our database for given server id with given project id.
	 *
	 * @param int $serverId Gerrit server id
	 * @param int $projectId Id of Gerrit project
	 * @return array
	 */
	public function getGerritProjectById($serverId, $projectId) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$query = 'SELECT `id`, `name` FROM ' . Database::TABLE_PROJECT . '
				  WHERE `server_id` = :server_id AND `id` = :id';
		$statement = $dbHandle->prepare($query);

		$statement->bindParam(':server_id', $serverId, \PDO::PARAM_INT);
		$statement->bindParam(':id', $projectId, \PDO::PARAM_INT);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Returns a approval by the unique identifier
	 *
	 * @param int $patchSetId ID of a record from the patchset table
	 * @param string $type Type of the approval
	 * @param int $by Person of the approval
	 * @return mixed
	 */
	protected function getGerritApprovalByIdentifier($patchSetId, $type, $by) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$query = 'SELECT `id`, `patchset`, `type`, `description`, `value`, `granted_on`, `by`
				  FROM ' . Database::TABLE_APPROVAL . '
				  WHERE `patchset` = :patchset
						AND `type` = :type
						AND `by` = :by';
		$statement = $dbHandle->prepare($query);

		$statement->bindParam(':patchset', $patchSetId, \PDO::PARAM_INT);
		$statement->bindParam(':type', $type, \PDO::PARAM_STR);
		$statement->bindParam(':by', $by, \PDO::PARAM_INT);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Returns a patchset by the unique identifier
	 *
	 * @param int $changeSetId ID of a record from the changeset table
	 * @param int $number Number of the patch set
	 * @param string $revision Revision of the patch set
	 * @param int $createdOn Timestamp of creation time
	 * @return mixed
	 */
	protected function getGerritPatchsetByIdentifier($changeSetId, $number, $revision, $createdOn) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$query = 'SELECT `id`, `changeset`, `number`, `revision`, `ref`, `uploader`, `created_on`
				  FROM ' . Database::TABLE_PATCHSET . '
				  WHERE `changeset` = :changeset
						AND `number` = :number
						AND `revision` = :revision
						AND `created_on` = :created_on';
		$statement = $dbHandle->prepare($query);

		$statement->bindParam(':changeset', $changeSetId, \PDO::PARAM_INT);
		$statement->bindParam(':number', $number, \PDO::PARAM_INT);
		$statement->bindParam(':revision', $revision, \PDO::PARAM_STR);
		$statement->bindParam(':created_on', $createdOn, \PDO::PARAM_INT);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	// @todo comment
	protected function getGerritFileCommentByIdentifier($patchSetId, $file, $line, $reviewer) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$query = 'SELECT `id`, `patchset`, `file`, `line`, `reviewer`, `message`
				  FROM ' . Database::TABLE_FILE_COMMENTS . '
				  WHERE `patchset` = :patchset
						AND `file` = :file
						AND `line` = :line
						AND `reviewer` = :reviewer';
		$statement = $dbHandle->prepare($query);

		$statement->bindParam(':patchset', $patchSetId, \PDO::PARAM_INT);
		$statement->bindParam(':file', $file, \PDO::PARAM_INT);
		$statement->bindParam(':line', $line, \PDO::PARAM_INT);
		$statement->bindParam(':reviewer', $reviewer, \PDO::PARAM_INT);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Returns a Comment by the unique identifier
	 *
	 * @param int $changeSetId ID of a record from the changeset table
	 * @param int $timestamp Timestamp of the comment
	 * @param int $reviewer Reviewer of the comment
	 * @param int $number Number (order number) of the comment
	 * @return mixed
	 */
	protected function getGerritCommentByIdentifier($changeSetId, $reviewer, $timestamp, $number) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$query = 'SELECT `id`, `changeset`, `timestamp`, `reviewer`, `message`
				  FROM ' . Database::TABLE_COMMENT . '
				  WHERE `changeset` = :changeset
						AND `timestamp` = :timestamp
						AND `reviewer` = :reviewer
						AND `number` = :number';
		$statement = $dbHandle->prepare($query);

		$statement->bindParam(':changeset', $changeSetId, \PDO::PARAM_INT);
		$statement->bindParam(':timestamp', $timestamp, \PDO::PARAM_INT);
		$statement->bindParam(':reviewer', $reviewer, \PDO::PARAM_INT);
		$statement->bindParam(':number', $number, \PDO::PARAM_STR);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Returns a changeset by the unique identifier
	 *
	 * @param int $project ID of a record from the project table
	 * @param int $branch ID of a record from the branch table
	 * @param string $id Change id of changeset
	 * @param int $createdOn Timestamp of creation time
	 * @return mixed
	 */
	protected function getGerritChangesetByIdentifier($project, $branch, $id, $createdOn) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$query = 'SELECT `id`, `project`, `branch`, `topic`, `identifier`, `number`, `subject`, `owner`, `url`, `commit_message`,
						 `created_on`, `last_updated`, `sort_key`, `open`, `status`, `current_patchset`
				  FROM ' . Database::TABLE_CHANGESET . '
				  WHERE `project` = :project
						AND `branch` = :branch
						AND `identifier` = :id
						AND `created_on` = :created_on';
		$statement = $dbHandle->prepare($query);

		$statement->bindParam(':project', $project, \PDO::PARAM_INT);
		$statement->bindParam(':branch', $branch, \PDO::PARAM_INT);
		$statement->bindParam(':id', $id, \PDO::PARAM_STR);
		$statement->bindParam(':created_on', $createdOn, \PDO::PARAM_INT);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Returns lookup values from a database table
	 *
	 * @param string $table Lookup tablename
	 * @param array $selectFields Array of fields to select
	 * @param array $whereCondition Array of where parts. Include prepared statement identifiers like :name
	 * @param array $whereValues Array of prepared statement identifiers like :name and fitted valued
	 * @return mixed
	 */
	protected function getLookupTableValues($table, array $selectFields, array $whereParts) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$whereCondition = $whereValues = array();
		foreach ($whereParts as $field => $value) {
			$whereCondition[] = '`' . $field . '` = :' . $field;
			$whereValues[':' . $field] = $value;
		}

		$query = 'SELECT `' . implode('`,`', $selectFields) . '`
				  FROM ' . $table . '
				  WHERE ' . implode(' AND ', $whereCondition) . '
				  LIMIT 1';

		$statement = $dbHandle->prepare($query);
		$executeResult = $statement->execute($whereValues);

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Checks if the given Gerrit server is known by the database.
	 * If the don`t know this host, we save this to the database.
	 *
	 * @param string $name Configured name of Gerrit server
	 * @param string $host Host of Gerrit Server
	 * @return integer
	 */
	public function proceedServer($name, $host) {
		$this->outputHeadline('Proceed Server');
		$this->output('Server "' . $name . '" (' . $host . ')');

		$serverId = $this->existsGerritServer($name, $host);

		// If the don`t know this server, save it!
		if ($serverId === false) {

			$serverData = array(
				'name' => $name,
				'host' => $host
			);
			$serverId = $this->insertRecord(Database::TABLE_SERVER, $serverData);
			$this->setServersFirstRun();

			$this->output('=> Inserted (ID: ' . $serverId . ')');

		} else {
			$this->output('=> Exists');
		}

		$this->output('');

		$this->setServerId($serverId);

		return $serverId;
	}

	protected function outputHeadline($message) {
		$this->output('#');
		$this->output('# ' . $message);
		$this->output('#');
	}

	/**
	 * If this import run is the first import run of this Gerrit server (see config['Host']),
	 * there must not be any update of imported records.
	 *
	 * This method is to detect errors / not unique identifiers between different Gerrit server
	 *
	 * @param string $level Level of importer. E.g. projects, changesets, ...
	 * @param int $exceptionCode Individual exception code for easier error detection
	 * @param mixed $debugInformation Any kind of debug information
	 * @throws \Exception
	 */
	protected function checkIfServersFirstRun($level, $exceptionCode, $debugInformation) {
		if ($this->isServersFirstRun() === true) {
			var_dump($debugInformation);

			$exceptionMessage = 'UPDATE DETECTED! ';
			$exceptionMessage .= 'This is the first run of server "' . $this->config['Host'] . '". ';
			$exceptionMessage .= 'There must not be an update in level ' . $level . '.';
			throw new \Exception($exceptionMessage, $exceptionCode);
		}
	}

	/**
	 * Imports all incoming projects.
	 * We save name, description and parent project.
	 *
	 * @param array $projects Projects from Gerrit API call
	 * @return void
	 */
	protected function importProjects(array $projects) {
		$parentMapping = array();

		// Loop over projects to proceed every single project
		foreach($projects as $name => $info) {
			$this->importProject($name, $info, $parentMapping);
		}

		// Correct parent / child relation of projects
		$this->proceedProjectParentChildRelations($parentMapping);
	}

	/**
	 * Imports a single project.
	 * We save name, description and parent project.
	 *
	 * @param string $name Project name
	 * @param array $info Project info like description or parent project
	 * @param array $parentMapping Array where parent / child releation will be saved
	 * @return int
	 */
	public function importProject($name, array $info, array &$parentMapping) {
		$this->output('Project "' . $name . '"');

		$row = $this->existsGerritProject($name, $this->getServerId());

		$projectRow = array(
			'server_id' => $this->getServerId(),
			'name' => $name,
			'description' => ((isset($info['description']) === true) ? $info['description']: '')
		);

		// If we don`t know this project, save this!
		if($row === false) {
			$projectRow['parent'] = 0;
			$id = $this->insertRecord(Database::TABLE_PROJECT, $projectRow);

			$this->output('=> Inserted (ID: ' . $id . ')');

			// If we know this project, lets check if there something new
		} else {

			$this->checkIfServersFirstRun('Projects', 1363893021, $row);

			$id = $row['id'];
			unset($row['id']);

			$diff = array_diff($projectRow, $row);

			// If there some new data for us, update it.
			if (count($diff) > 0) {
				$this->updateRecord(Database::TABLE_PROJECT, $diff, $id);

				$this->output('=> Updated (ID: ' . $id . ')');

			} else {
				$this->output('=> Nothing new. Skip it');
			}
		}

		// We have to save the parent / child relations of projects to execute bulk updates afterwards
		if (isset($info['parent']) === true) {
			$parentMapping[$info['parent']][] = intval($id);
		}

		$info = $this->unsetKeys($info, array('description', 'parent'));
		$this->checkIfAllValuesWereProceeded($info, 'Project');

		return $id;
	}

	/**
	 * Set correct parent / child relation of projects in database
	 *
	 * @param array $parentMapping
	 * @return void
	 */
	public function proceedProjectParentChildRelations(array $parentMapping) {
		// If there are no parent / child relations for projects, skip it.
		if (count($parentMapping) == 0) {
			return;
		}

		$this->output('');
		$this->outputHeadline('Proceed Project parent / child relation');

		$parentProjects = $this->getGerritProjectsByName($this->getServerId(), array_keys($parentMapping));
		foreach ($parentProjects as $parentProject) {
			$dataToUpdate = array(
				'parent' => intval($parentProject['id'])
			);

			// The IN(id list) is not working here. I don`t know why.
			// If anyone has an idea, please let me know.
			$where = 'FIND_IN_SET(`id`, \'' . implode(',', $parentMapping[$parentProject['name']]) . '\') > 0 ';
			$where .= 'AND  parent != ' . $dataToUpdate['parent'];
			$updatedRows = $this->updateRecords(Database::TABLE_PROJECT, $dataToUpdate, $where);

			$this->output('=> ' . $updatedRows . ' projects updated (with "' . $parentProject['name'] . '" as parent project');
		}
	}

	/**
	 * Inserts a single record (given $data) in the given $table via prepared statements.
	 *
	 * @param string $table Table to insert
	 * @param array $data Data to insert
	 * @return int Last inserted id
	 */
	protected function insertRecord($table, array $data) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$fieldSet = array_keys($data);

		// Prepare sets
		$valueSet = array();
		foreach ($data as $key => $value) {
			$valueSet[':' . $key ] = $value;
		}

		if (count($fieldSet) == 0 || count($valueSet) == 0) {
			throw new \Exception('Missing data for insert query', 1363894664);
		}

		$fieldSet[] = 'tstamp';
		$valueSet[':tstamp'] = time();

		$fieldSet[] = 'crdate';
		$valueSet[':crdate'] = time();

		$query = 'INSERT INTO ' . $table . ' (`' . implode('`,`', $fieldSet) . '`)
				  VALUES (' . implode(', ', array_keys($valueSet)) . ')';

		$statement = $dbHandle->prepare($query);
		$executeResult = $statement->execute($valueSet);

		$this->database->checkQueryError($statement, $executeResult);
		return $dbHandle->lastInsertId();
	}

	/**
	 * Updates a single record (given $id) in the given $table with the given $data via prepared statements.
	 *
	 * @param string $table Table to update
	 * @param array $data New data
	 * @param int $id ID of record to update
	 * @return int
	 */
	protected function updateRecord($table, array $data, $id) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();
		list($updateSet, $prepareSet) = $this->prepareUpdateData($data);

		$prepareSet[':id'] = $id;

		$query = 'UPDATE ' . $table . '
				  SET ' . implode(', ', $updateSet) . '
				  WHERE `id` = :id';

		$statement = $dbHandle->prepare($query);
		$executeResult = $statement->execute($prepareSet);

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->rowCount();
	}

	/**
	 * Updates all records which matches the $where statement in the given $table with the given $data via prepared statements.
	 *
	 * @param string $table Table to update
	 * @param array $data New data
	 * @param string $where Where statement
	 * @return int
	 */
	protected function updateRecords($table, array $data, $where) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();
		list($updateSet, $prepareSet) = $this->prepareUpdateData($data);

		$query = 'UPDATE ' . $table . '
				  SET ' . implode(', ', $updateSet) . '
				  WHERE ' . $where;

		$statement = $dbHandle->prepare($query);
		$executeResult = $statement->execute($prepareSet);

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->rowCount();
	}

	/**
	 * Prepared the needed data / structures for prepared statements for our UPDATE queries.
	 *
	 * After this we get two structures:
	 * 1. ($updateSet): array(0 => 'MySQLfield1 = :MySQLfield1', 1 => 'MySQLfield2 = :MySQLfield2', ...);
	 * 2. ($prepareSet): array(':MySQLfield1' => 'valueToUpdate1', ':MySQLfield2' => 'valueToUpdate1', ...);
	 *
	 * @see $this->updateRecord
	 * @see $this->updateRecords
	 *
	 * @param array $data Data to update with fields as keys and related values as values
	 * @return array
	 * @throws \Exception
	 */
	protected function prepareUpdateData(array $data) {
		$updateSet = array();
		$prepareSet = array();
		foreach ($data as $key => $value) {
			$updateSet[] = '`' . $key . '` = :' . $key;
			$prepareSet[':' . $key] = $value;
		}

		if (count($updateSet) == 0 || count($prepareSet) == 0) {
			throw new \Exception('Missing data for update query', 1363894675);
		}

		$updateSet[] = '`tstamp` = :tstamp';
		$prepareSet[':tstamp'] = time();

		return array($updateSet, $prepareSet);
	}

	protected function transferJsonToArray($json) {
		return json_decode($json, true);
	}

	/**
	 * Checks if a Gerrit server is known by our database.
	 *
	 * @param string $name Name of configured Gerrit server
	 * @param string $host Host of configured Gerrit server
	 * @return string
	 */
	protected function existsGerritServer($name, $host) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$query = 'SELECT `id`
				  FROM ' . Database::TABLE_SERVER . '
				  WHERE `name` = :name AND `host` = :host LIMIT 1';

		$statement = $dbHandle->prepare($query);
		$executeResult = $statement->execute(array(':name' => $name, ':host' => $host));

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetchColumn();
	}

	/**
	 * Checks if a given project $name is known for the given Gerrit server $serverId
	 *
	 * @param string $name Name of project
	 * @param int $serverId Server id of Gerrit server
	 * @return array
	 */
	protected function existsGerritProject($name, $serverId) {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		// We use BINARY here, because we need a case sensitive check
		$query = 'SELECT `id`, `server_id`, `name`, `description`
				  FROM ' . Database::TABLE_PROJECT . '
				  WHERE `server_id` = :server_id
				  AND BINARY `name` = :name LIMIT 1';

		$statement = $dbHandle->prepare($query);
		$statement->bindParam(':name', $name, \PDO::PARAM_STR);
		$statement->bindParam(':server_id', $serverId, \PDO::PARAM_INT);
		$executeResult = $statement->execute();

		$this->database->checkQueryError($statement, $executeResult);
		return $statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Truncates the temp tables
	 *
	 * @return void
	 */
	protected function cleanupTempTables() {
		$dbHandle = $this->getDatabase()->getDatabaseConnection();

		$query = 'TRUNCATE ' . Database::TABLE_TMP_DEPENDS_NEEDED;
		$dbHandle->query($query);
	}
}