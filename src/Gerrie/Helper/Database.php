<?php

namespace Gerrie\Helper;

class Database {

	/**
	 * Database handle
	 *
	 * @var null|\PDO
	 */
	protected $handle = null;

	/**
	 * Table constants
	 *
	 * @var string
	 */
	const TABLE_SERVER               = 'gerrit_server';
	const TABLE_PROJECT              = 'gerrit_project';
	const TABLE_BRANCH               = 'gerrit_branch';
	const TABLE_CHANGESET            = 'gerrit_changeset';
	const TABLE_PERSON               = 'gerrit_person';
	const TABLE_EMAIL                = 'gerrit_email';
	const TABLE_PATCHSET             = 'gerrit_patchset';
	const TABLE_FILES                = 'gerrit_files';
	const TABLE_APPROVAL             = 'gerrit_approval';
	const TABLE_COMMENT              = 'gerrit_comment';
	const TABLE_STATUS               = 'gerrit_changeset_status';
	const TABLE_FILEACTION           = 'gerrit_file_action';
	const TABLE_TRACKING_ID          = 'gerrit_tracking_ids';
	const TABLE_TRACKING_SYSTEM      = 'gerrit_tracking_system';
	const TABLE_SUBMIT_RECORDS       = 'gerrit_submit_records';
	const TABLE_SUBMIT_RECORD_LABELS = 'gerrit_submit_record_labels';
	const TABLE_FILE_COMMENTS        = 'gerrit_file_comments';
	const TABLE_TMP_DEPENDS_NEEDED   = 'gerrit_tmp_depends_needed';
	const TABLE_CHANGESET_NEEDEDBY   = 'gerrit_changeset_neededby';

	/**
	 * Field value constants for:
	 *    Table gerrit_tmp_depends_needed
	 *    Field: status
	 *
	 * Records source is the 'dependsOn' property of a changeset
	 *
	 * @var int
	 */
	const TMP_DEPENDS_NEEDED_STATUS_DEPENDSON = 1;

	/**
	 * Field value constants for:
	 *    Table gerrit_tmp_depends_needed
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
		'gerrit_server' => "
			CREATE TABLE `gerrit_server` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`host` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		'gerrit_project' => "
			CREATE TABLE `gerrit_project` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`server_id` int(11) unsigned NOT NULL,
				`name` varchar(255) NOT NULL DEFAULT '',
				`description` TEXT NOT NULL DEFAULT '',
				`parent` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		'gerrit_branch' => "
			CREATE TABLE `gerrit_branch` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		// @todo was ist id für ein string? SHA1 + Ein Zeichen? Wenn Ja, Feld auf 41 Zeichen begrenzen
		// @todo prüfen ob sortKey immer eine feste länge hat
		'gerrit_changeset' => "
			CREATE TABLE `gerrit_changeset` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`project` int(11) unsigned NOT NULL DEFAULT 0,
				`branch` int(11) unsigned NOT NULL DEFAULT 0,
				`topic` varchar(255) NOT NULL DEFAULT '',
				`identifier` varchar(100) NOT NULL DEFAULT '',
				`number` int(11) unsigned NOT NULL DEFAULT 0,
				`subject` varchar(255) NOT NULL DEFAULT '',
				`owner` int(11) unsigned NOT NULL DEFAULT 0,
				`url` varchar(255) NOT NULL DEFAULT '',
				`commit_message` text NOT NULL,
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

		'gerrit_person' => "
			CREATE TABLE `gerrit_person` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`username` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		'gerrit_email' => "
			CREATE TABLE `gerrit_email` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`person` int(11) unsigned NOT NULL DEFAULT 0,
				`email` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		'gerrit_patchset' => "
			CREATE TABLE `gerrit_patchset` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`changeset` int(11) unsigned NOT NULL DEFAULT 0,
				`number` int(11) unsigned NOT NULL DEFAULT 0,
				`revision` varchar(255) NOT NULL DEFAULT '',
				`ref` varchar(255) NOT NULL DEFAULT '',
				`uploader` int(11) unsigned NOT NULL DEFAULT 0,
				`created_on` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		// @todo Feld type auslagern
		'gerrit_files' => "
			CREATE TABLE `gerrit_files` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`patchset` int(11) unsigned NOT NULL DEFAULT 0,
				`file` varchar(255) NOT NULL DEFAULT '',
				`type` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		// @todo feld type und description soviel speicherplatz?
		'gerrit_approval' => "
			CREATE TABLE `gerrit_approval` (
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

		'gerrit_comment' => "
			CREATE TABLE `gerrit_comment` (
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

		'gerrit_changeset_status' => "
			CREATE TABLE `gerrit_changeset_status` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		'gerrit_file_action' => "
			CREATE TABLE `gerrit_file_action` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		// @todo is number really a number?
		'gerrit_tracking_ids' => "
			CREATE TABLE `gerrit_tracking_ids` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`changeset` int(11) unsigned NOT NULL DEFAULT 0,
				`system` int(11) unsigned NOT NULL DEFAULT 0,
				`number` varchar(255) NOT NULL DEFAULT '',
				`referenced_earlier` int(1) NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		'gerrit_tracking_system' => "
			CREATE TABLE `gerrit_tracking_system` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		// @todo refactor status = int
		'gerrit_submit_records' => "
			CREATE TABLE `gerrit_submit_records` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`changeset` int(11) unsigned NOT NULL DEFAULT 0,
				`status` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				UNIQUE KEY `changeset` (`changeset`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		'gerrit_submit_record_labels' => "
			CREATE TABLE `gerrit_submit_record_labels` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`submit_record` int(11) unsigned NOT NULL DEFAULT 0,
				`label` varchar(255) NOT NULL DEFAULT '',
				`status` varchar(255) NOT NULL DEFAULT '',
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				UNIQUE KEY `label_per_record` (`submit_records`, `label`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		'gerrit_file_comments' => "
			CREATE TABLE `gerrit_file_comments` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`patchset` int(11) unsigned NOT NULL DEFAULT 0,
				`file` int(11) unsigned NOT NULL DEFAULT 0,
				`line` int(11) unsigned NOT NULL DEFAULT 0,
				`reviewer` int(11) unsigned NOT NULL DEFAULT 0,
				`message` text NOT NULL,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

		// Temp table for 'dependsOn' and 'neededBy'
		// Status: 1 => dependsOn, 2 => neededBy
		'gerrit_tmp_depends_needed' => "
			CREATE TABLE `gerrit_tmp_depends_needed` (
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

		'gerrit_changeset_neededby' => "
			CREATE TABLE `gerrit_changeset_neededby` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`changeset` int(11) unsigned NOT NULL DEFAULT 0,
				`needed_by` int(11) unsigned NOT NULL DEFAULT 0,
				`tstamp` int(11) unsigned NOT NULL DEFAULT 0,
				`crdate` int(11) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				UNIQUE KEY `changeset_needed` (`changeset`,`needed_by`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	);

	public function __construct(array $config) {
		// Build the port part of DSN
		$portPart = (isset($config['Port']) === true) ? intval($config['Port']): null;
		if ($portPart > 0) {
			$portPart = 'port=' . $portPart . ';';
		}

		$dsn = 'mysql:host=' . $config['Host'] . ';' . $portPart . 'dbname=' . $config['Name'];

		$this->handle = new \PDO($dsn,  $config['Username'], $config['Password']);
	}

	public function getDatabaseConnection() {
		return $this->handle;
	}

	public function getTableDefinition() {
		return $this->tableDefinition;
	}

	public function checkQueryError(\PDOStatement $statement, $lastQueryResult) {
		if ($lastQueryResult === true) {
			return;
		}

		$errorInfo = $statement->errorInfo();
		throw new \Exception($errorInfo[2] . ' (' . $errorInfo[1] . ')', 1367873943);
	}
}