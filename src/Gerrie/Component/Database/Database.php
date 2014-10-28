<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Component\Database;

class Database
{

    /**
     * Database handle
     *
     * @var null|\PDO
     */
    protected $handle = null;

    /**
     * Database credentials
     *
     * @var array
     */
    private $config = array();

    /**
     * Table constants
     *
     * @var string
     */
    const TABLE_SERVER = 'gerrie_server';
    const TABLE_PROJECT = 'gerrie_project';
    const TABLE_BRANCH = 'gerrie_branch';
    const TABLE_CHANGESET = 'gerrie_changeset';
    const TABLE_PERSON = 'gerrie_person';
    const TABLE_EMAIL = 'gerrie_email';
    const TABLE_PATCHSET = 'gerrie_patchset';
    const TABLE_FILES = 'gerrie_files';
    const TABLE_APPROVAL = 'gerrie_approval';
    const TABLE_COMMENT = 'gerrie_comment';
    const TABLE_STATUS = 'gerrie_changeset_status';
    const TABLE_FILEACTION = 'gerrie_file_action';
    const TABLE_TRACKING_ID = 'gerrie_tracking_ids';
    const TABLE_TRACKING_SYSTEM = 'gerrie_tracking_system';
    const TABLE_SUBMIT_RECORDS = 'gerrie_submit_records';
    const TABLE_SUBMIT_RECORD_LABELS = 'gerrie_submit_record_labels';
    const TABLE_FILE_COMMENTS = 'gerrie_file_comments';
    const TABLE_TMP_DEPENDS_NEEDED = 'gerrie_tmp_depends_needed';
    const TABLE_CHANGESET_NEEDEDBY = 'gerrie_changeset_neededby';

    /**
     * Field value constants for:
     *    Table gerrie_tmp_depends_needed
     *    Field: status
     *
     * Records source is the 'dependsOn' property of a changeset
     *
     * @var int
     */
    const TMP_DEPENDS_NEEDED_STATUS_DEPENDSON = 1;

    /**
     * Field value constants for:
     *    Table gerrie_tmp_depends_needed
     *    Field: status
     *
     * Records source is the 'neededBy' property of a changeset
     *
     * @var int
     */
    const TMP_DEPENDS_NEEDED_STATUS_NEEDEDBY = 2;

    /**
     * MySQL table definition for needed database tables
     *
     * @var array
     */
    protected $tableDefinition = array(
        // @todo add unique stuff
        'gerrie_server' => "
			CREATE TABLE `gerrie_server` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`host` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_project' => "
			CREATE TABLE `gerrie_project` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`server_id` int(11) unsigned NOT NULL,
				`identifier` varchar(255) NOT NULL DEFAULT '',
				`name` varchar(255) NOT NULL DEFAULT '',
				`description` TEXT NOT NULL,
				`kind` varchar(255) NOT NULL DEFAULT '',
				`state` varchar(255) NOT NULL DEFAULT '',
				`parent` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_branch' => "
			CREATE TABLE `gerrie_branch` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        // @todo was ist id für ein string? SHA1 + Ein Zeichen? Wenn Ja, Feld auf 41 Zeichen begrenzen
        // @todo prüfen ob sortKey immer eine feste länge hat
        'gerrie_changeset' => "
			CREATE TABLE `gerrie_changeset` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`project` int(11) unsigned NOT NULL DEFAULT 0,
				`branch` int(11) unsigned NOT NULL DEFAULT 0,
				`topic` varchar(255) NOT NULL DEFAULT '',
				`identifier` varchar(100) NOT NULL DEFAULT '',
				`number` int(11) unsigned NOT NULL DEFAULT 0,
				`subject` varchar(255) NOT NULL DEFAULT '',
				`owner` int(11) unsigned NOT NULL DEFAULT 0,
				`url` varchar(255) NOT NULL DEFAULT '',
				`commit_message` MEDIUMTEXT NOT NULL,
				`created_on` int(11) unsigned NOT NULL DEFAULT 0,
				`last_updated` int(11) unsigned NOT NULL DEFAULT 0,
				`sort_key` varchar(255) NOT NULL DEFAULT '',
				`open` int(11) unsigned NOT NULL DEFAULT 0,
				`status` int(11) unsigned NOT NULL DEFAULT 0,
				`current_patchset` int(11) unsigned NOT NULL DEFAULT 0,
				`depends_on` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_person' => "
			CREATE TABLE `gerrie_person` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`username` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_email' => "
			CREATE TABLE `gerrie_email` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`person` int(11) unsigned NOT NULL DEFAULT 0,
				`email` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_patchset' => "
			CREATE TABLE `gerrie_patchset` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`changeset` int(11) unsigned NOT NULL DEFAULT 0,
				`number` int(11) unsigned NOT NULL DEFAULT 0,
				`revision` varchar(255) NOT NULL DEFAULT '',
				`ref` varchar(255) NOT NULL DEFAULT '',
				`uploader` int(11) unsigned NOT NULL DEFAULT 0,
				`author` int(11) unsigned NOT NULL DEFAULT 0,
				`size_insertions` int(11) NOT NULL DEFAULT 0,
				`size_deletions` int(11) NOT NULL DEFAULT 0,
				`is_draft` TINYINT unsigned NOT NULL DEFAULT 0,
				`created_on` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        // @todo Feld type auslagern
        'gerrie_files' => "
			CREATE TABLE `gerrie_files` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`patchset` int(11) unsigned NOT NULL DEFAULT 0,
				`file` varchar(255) NOT NULL DEFAULT '',
				`file_old` varchar(255) NOT NULL DEFAULT '',
				`type` int(11) unsigned NOT NULL DEFAULT 0,
				`insertions` int(11) unsigned NOT NULL DEFAULT 0,
				`deletions` int(11) NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        // @todo feld type und description soviel speicherplatz?
        'gerrie_approval' => "
			CREATE TABLE `gerrie_approval` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`patchset` int(11) unsigned NOT NULL DEFAULT 0,
				`type` varchar(255) NOT NULL DEFAULT '',
				`description` varchar(255) NOT NULL DEFAULT '',
				`value` int(11) signed NOT NULL DEFAULT 0,
				`granted_on` int(11) unsigned NOT NULL DEFAULT 0,
				`by` int(11) unsigned NOT NULL DEFAULT 0,
				`voted_earlier` int(1) NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_comment' => "
			CREATE TABLE `gerrie_comment` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`changeset` int(11) unsigned NOT NULL DEFAULT 0,
				`timestamp` int(11) unsigned NOT NULL DEFAULT 0,
				`reviewer` int(11) unsigned NOT NULL DEFAULT 0,
				`message` text NOT NULL,
				`number` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_changeset_status' => "
			CREATE TABLE `gerrie_changeset_status` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_file_action' => "
			CREATE TABLE `gerrie_file_action` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        // @todo is number really a number?
        'gerrie_tracking_ids' => "
			CREATE TABLE `gerrie_tracking_ids` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`changeset` int(11) unsigned NOT NULL DEFAULT 0,
				`system` int(11) unsigned NOT NULL DEFAULT 0,
				`number` varchar(255) NOT NULL DEFAULT '',
				`referenced_earlier` int(1) NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_tracking_system' => "
			CREATE TABLE `gerrie_tracking_system` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        // @todo refactor status = int
        'gerrie_submit_records' => "
			CREATE TABLE `gerrie_submit_records` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`changeset` int(11) unsigned NOT NULL DEFAULT 0,
				`status` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				UNIQUE KEY `changeset` (`changeset`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_submit_record_labels' => "
			CREATE TABLE `gerrie_submit_record_labels` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`submit_record` int(11) unsigned NOT NULL DEFAULT 0,
				`label` varchar(255) NOT NULL DEFAULT '',
				`status` varchar(255) NOT NULL DEFAULT '',
				`by` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				UNIQUE KEY `label_per_record` (`submit_record`, `label`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_file_comments' => "
			CREATE TABLE `gerrie_file_comments` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`patchset` int(11) unsigned NOT NULL DEFAULT 0,
				`file` int(11) unsigned NOT NULL DEFAULT 0,
				`line` int(11) unsigned NOT NULL DEFAULT 0,
				`reviewer` int(11) unsigned NOT NULL DEFAULT 0,
				`message` text NOT NULL,
				`message_crc32` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        // Temp table for 'dependsOn' and 'neededBy'
        // Status: 1 => dependsOn, 2 => neededBy
        'gerrie_tmp_depends_needed' => "
			CREATE TABLE `gerrie_tmp_depends_needed` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`changeset` int(11) unsigned NOT NULL DEFAULT 0,
				`identifier` varchar(100) NOT NULL DEFAULT '',
				`number` int(11) unsigned NOT NULL DEFAULT 0,
				`revision` varchar(255) NOT NULL DEFAULT '',
				`ref` varchar(255) NOT NULL DEFAULT '',
				`is_current_patchset` int(1) unsigned NOT NULL DEFAULT 0,
				`status` int(1) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'gerrie_changeset_neededby' => "
			CREATE TABLE `gerrie_changeset_neededby` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`changeset` int(11) unsigned NOT NULL DEFAULT 0,
				`needed_by` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				UNIQUE KEY `changeset_needed` (`changeset`,`needed_by`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    );

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect($config);
    }

    private function connect($config) {
        // Build the port part of DSN
        $portPart = (isset($config['Port']) === true) ? intval($config['Port']) : null;
        if ($portPart > 0) {
            $portPart = 'port=' . $portPart . ';';
        }

        $dsn = 'mysql:host=' . $config['Host'] . ';' . $portPart . 'dbname=' . $config['Name'];

        $this->handle = new \PDO($dsn, $config['Username'], $config['Password']);
    }

    public function getDatabaseConnection()
    {
        return $this->handle;
    }

    public function getTableDefinition()
    {
        return $this->tableDefinition;
    }

    public function checkQueryError(\PDOStatement $statement, $lastQueryResult, $prepareSet = array())
    {
        if ($lastQueryResult === true) {
            return $statement;
        }

        // MySQL server has gone away ... automatic reconnect
        // The connection can be lost if the mysql.connection_timeout or default_socket_timeout is to low.
        // Here we try to reconnect to the database and execute the statement again
        $errorInfo = $statement->errorInfo();
        if ($errorInfo[1] && $errorInfo[1] == 2006) {
            $this->connect($this->config);
            if (count($prepareSet) > 0) {
                $statement->execute($prepareSet);
            } else {
                $statement->execute();
            }

        } elseif ($errorInfo[1]) {
            throw new \Exception($errorInfo[2] . ' (' . $errorInfo[1] . ')', 1367873943);
        }

        return $statement;
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
    protected function prepareUpdateData(array $data)
    {
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

    /**
     * Updates all records which matches the $where statement in the given $table with the given $data via prepared statements.
     *
     * @param string $table Table to update
     * @param array $data New data
     * @param string $where Where statement
     * @return int
     */
    public function updateRecords($table, array $data, $where)
    {
        $dbHandle = $this->getDatabaseConnection();
        list($updateSet, $prepareSet) = $this->prepareUpdateData($data);

        $query = 'UPDATE ' . $table . '
                  SET ' . implode(', ', $updateSet) . '
                  WHERE ' . $where;

        $statement = $dbHandle->prepare($query);
        $executeResult = $statement->execute($prepareSet);

        $statement = $this->checkQueryError($statement, $executeResult, $prepareSet);
        return $statement->rowCount();
    }

    /**
     * Updates a single record (given $id) in the given $table with the given $data via prepared statements.
     *
     * @param string $table Table to update
     * @param array $data New data
     * @param int $id ID of record to update
     * @return int
     */
    public function updateRecord($table, array $data, $id)
    {
        $dbHandle = $this->getDatabaseConnection();
        list($updateSet, $prepareSet) = $this->prepareUpdateData($data);

        $prepareSet[':id'] = $id;

        $query = 'UPDATE ' . $table . '
                  SET ' . implode(', ', $updateSet) . '
                  WHERE `id` = :id';

        $statement = $dbHandle->prepare($query);
        $executeResult = $statement->execute($prepareSet);

        $statement = $this->checkQueryError($statement, $executeResult, $prepareSet);
        return $statement->rowCount();
    }

    /**
     * Inserts a single record (given $data) in the given $table via prepared statements.
     *
     * @param string $table Table to insert
     * @param array $data Data to insert
     * @throws \Exception
     * @return int Last inserted id
     */
    public function insertRecord($table, array $data)
    {
        $dbHandle = $this->getDatabaseConnection();

        $fieldSet = array_keys($data);

        // Prepare sets
        $valueSet = array();
        foreach ($data as $key => $value) {
            $valueSet[':' . $key] = $value;
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

        $statement = $this->checkQueryError($statement, $executeResult, $valueSet);
        return $dbHandle->lastInsertId();
    }
}