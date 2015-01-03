<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Component\DataService;

interface DataServiceInterface
{

    /**
     * Sets the name of the data service
     *
     * @param string $name Name of data service
     * @return void
     */
    public function setName($name);

    /**
     * Returns the name of the data service
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the API connector
     *
     * @param \Buzz\Browser|\Gerrie\Component\Connection\SSH $connector API connector like HTTP Client
     * @return void
     */
    public function setConnector($connector);

    /**
     * Returns the API connector
     *
     * @return \stdClass
     */
    public function getConnector();

    /**
     * Sets the configuration
     *
     * @param array $config
     * @return void
     */
    public function setConfig(array $config);

    /**
     * Gets the configuration
     *
     * @return array
     */
    public function getConfig();

    /**
     * Sets the query limit
     *
     * @param int $queryLimit The query limit for Gerrit querys
     * @return void
     */
    public function setQueryLimit($queryLimit);

    /**
     * Gets the query limit
     *
     * @return int
     */
    public function getQueryLimit();

    /**
     * Gets the Host
     *
     * @return string
     */
    public function getHost();

    /**
     * Transforms a JSON string into an array.
     * Regular, the json is the content from the response.
     *
     * @param string $json The json string
     * @return array|null
     */
    public function transformJsonResponse($json);

    /**
     * Requests projects at the Gerrit server
     *
     * @return array|null
     */
    public function getProjects();

    /**
     * Requests changesets at the Gerrit server.
     *
     * @param string $projectName The project name
     * @param string $resumeKey The key where the request will be resumed
     * @param integer $start Number of changesets which will be skipped
     * @throws \Exception
     */
    public function getChangesets($projectName, $resumeKey = null, $start = 0);

    /**
     * Returns the version of the Gerrit server
     *
     * @link https://review.typo3.org/Documentation/cmd-version.html
     *
     * @return string
     */
    public function getVersion();
}