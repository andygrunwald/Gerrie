<?php
/**
 * This file is part of the TYPO3-Analytics package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\DataService;

class SSH extends Base {

	/**
	 * Constructor
	 *
	 * @param \Gerrie\Helper\SSH $connector
	 * @param array $config
	 * @return \Gerrie\DataService\SSH
	 */
	public function __construct(\Gerrie\Helper\SSH $connector, array $config) {
		$this->setConnector($connector);
		$this->setConfig($config);
		$this->setName('SSH');
	}

	/**
	 * Transforms a JSON string into an array.
	 * Regular, the json is the content from the response.
	 *
	 * @param string $json The json string
	 * @return array|null
	 */
	public function transformJsonResponse($json) {
		return json_decode($json, true);
	}

	/**
	 * Requests projects at the Gerrit server
	 *
	 * @return array|null
	 */
	public function getProjects() {
		$connector = $this->getBaseQuery();

		$connector->addCommandPart('ls-projects');
		$connector->addArgument('--format', 'JSON', ' ');
		$connector->addArgument('--description', '', '');
		$connector->addArgument('--tree', '', '');
		$connector->addArgument('--type', 'all', ' ');
		$connector->addArgument('--all', '', '');
		// The ls-projects command supports a "limit" argument
		// The default limit from Gerrit is 500.
		// What happen when the Gerrit system got more then 500 projects?
		// I don`t see a "resume_sortkey" option here :(
		// Does anyone know this?

		$content = $connector->execute();
		$content = $this->transformJsonResponse($content);

		return $content;
	}

	/**
	 * Gets the base ssh query object for all SSH requests.
	 *
	 * @return \Gerrie\Helper\SSH
	 */
	protected function getBaseQuery() {
		$config = $this->getConfig();
		$connector = $this->getConnector();

		$connector->reset();

		$host = $config['Host'];
		if (isset($config['Username']) === true) {
			$host = $config['Username'] . '@' . $host;
		}
		$connector->addCommandPart($host);
		$connector->addCommandPart('gerrit');

		return $connector;
	}

	/**
	 * Requests changesets at the Gerrit server.
	 *
	 * @param string $projectName The project name
	 * @param string $resumeKey The key where the request will be resumed
	 * @return array
	 * @throws \Exception
	 */
	public function getChangesets($projectName, $resumeKey = null) {
		$connector = $this->getBaseQuery();

		$connector->addCommandPart('query');
		$connector->addArgument('--format', 'JSON', ' ');
		$connector->addArgument('--current-patch-set', '', ' ');
		$connector->addArgument('--all-approvals', '', '');
		$connector->addArgument('--files', '', '');
		$connector->addArgument('--comments', '', '');
		$connector->addArgument('--commit-message', '', '');
		$connector->addArgument('--dependencies', '', '');
		$connector->addArgument('--submit-records', '', '');
		$connector->addArgument('', 'project:' . $projectName, '');
		$connector->addArgument('limit', $this->getQueryLimit(), ':');

		if ($resumeKey) {
			$connector->addArgument('resume_sortkey', $resumeKey, ':');
		}

		$content = $connector->execute(false);
		return $content;
	}

	/**
	 * Initiales the query limit
	 *
	 * @return int
	 */
	protected function initQueryLimit() {
		// @todo implement! Idea: Config OR try to get query limit over HTTP with HTTP dataservice
		return 500;
	}
}