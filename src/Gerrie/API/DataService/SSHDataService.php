<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\API\DataService;

class SSHDataService extends BaseDataService
{

    /**
     * Version of the Gerrit Server
     *
     * @var string
     */
    protected $version;

    /**
     * Constructor
     *
     * @param \Gerrie\Component\Connection\SSH $connector
     * @param array $config
     * @return \Gerrie\API\DataService\SSHDataService
     */
    public function __construct(\Gerrie\Component\Connection\SSH $connector, array $config)
    {
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
    public function transformJsonResponse($json)
    {
        return json_decode($json, true);
    }

    /**
     * Requests projects at the Gerrit server
     *
     * @return array|null
     */
    public function getProjects()
    {
        $connector = $this->getBaseQuery();

        $connector->addCommandPart('ls-projects');
        $connector->addArgument('--format', 'JSON', ' ');
        $connector->addArgument('--description', '', '');
        $connector->addArgument('--tree', '', '');
        $connector->addArgument('--type', 'all', ' ');
        $connector->addArgument('--all', '', '');
        // TODO
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
     * @return \Gerrie\Component\Connection\SSH
     */
    protected function getBaseQuery()
    {
        $config = $this->getConfig();
        $connector = $this->getConnector();
        /** @var \Gerrie\Component\Connection\SSH $connector  */

        $connector->reset();

        $host = $config['host'];
        if (isset($config['user']) === true) {
            $host = $config['user'] . '@' . $host;
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
     * @param integer $start Number of changesets which will be skipped
     * @return array
     * @throws \Exception
     */
    public function getChangesets($projectName, $resumeKey = null, $start = 0)
    {
        if (!$this->version) {
            $this->version = $this->getVersion();
        }

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

        // TODO Implement "--all-reviewers"
        // Show the name and email of all reviewers which are added to a change (irrespective of whether they have been voting on that change or not).
        // See https://review.typo3.org/Documentation/cmd-query.html

        /**
         * We have to compare the current version, because Gerrit got
         * a breaking change in v2.9.0. They removed the "resume_sortkey" parameter,
         * without offering a alternative for SSH API.
         * "--start" was added in v2.9.1
         *
         *  = v2.9.0 is not supported, because resume_sortkey was removed and --start not implemented
         * >= v2.9.1 is needed for --start parameter
         *
         * @link https://github.com/andygrunwald/Gerrie/issues/4
         * @link https://groups.google.com/forum/#!searchin/repo-discuss/Andy$20Grunwald/repo-discuss/yQgRR5hlS3E/xTAZWXOSklsJ
         */
        if ((version_compare($this->version, '2.9.1') >= 0) && $start > 0) {
            $connector->addArgument('--start', $start, ' ');

        } elseif (version_compare($this->version, '2.9.0') == 0) {
            throw new \RuntimeException('Version v2.9.0 of Gerrit is not supported with SSH API. See #4 in andygrunwald/Gerrie on Github.', 1412027367);

        } elseif ((version_compare($this->version, '2.9.0') == -1) && $resumeKey) {
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
    protected function initQueryLimit()
    {
        // @todo implement! Idea: Config OR try to get query limit over HTTP with HTTP dataservice
        return 500;
    }

    /**
     * Returns the version of the Gerrit server
     *
     * @link https://review.typo3.org/Documentation/cmd-version.html
     *
     * @return string
     */
    public function getVersion()
    {
        $connector = $this->getBaseQuery();
        $connector->addCommandPart('version');
        $content = $connector->execute(false);

        if (is_array($content) && count($content) > 0) {
            $content = array_shift($content);
            $content = str_replace('gerrit version ', '', $content);
        }

        return $content;
    }
}