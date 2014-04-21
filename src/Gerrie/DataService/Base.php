<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\DataService;

abstract class Base
{

    /**
     * Name of data service
     *
     * @var string
     */
    private $name = '';

    /**
     * API connector like HTTP or SSH
     *
     * @var object
     */
    private $connector = null;

    /**
     * Query limit
     *
     * @var int|null
     */
    private $queryLimit = null;

    /**
     * Configuration
     *
     * @var array
     */
    protected $config = array();

    /**
     * Sets the name of the data service
     *
     * @param string $name Name of data service
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }

    /**
     * Returns the name of the data service
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the API connector
     *
     * @param \stdClass $connector API connector like HTTP Client
     * @return void
     */
    public function setConnector($connector)
    {
        $this->connector = $connector;
    }

    /**
     * Returns the API connector
     *
     * @return \stdClass
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * Sets the configuration
     *
     * @param array $config
     * @return void
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * Gets the configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the query limit
     *
     * @param int $queryLimit The query limit for Gerrit querys
     * @return void
     */
    public function setQueryLimit($queryLimit)
    {
        $this->queryLimit = intval($queryLimit);
    }

    /**
     * Gets the query limit
     *
     * @return int
     */
    public function getQueryLimit()
    {

        if ($this->queryLimit === null) {
            $this->setQueryLimit($this->initQueryLimit());
        }

        return $this->queryLimit;
    }

    /**
     * Gets the Host
     *
     * @return string
     */
    public function getHost()
    {
        $config = $this->getConfig();

        return $config['Host'];
    }

    /**
     * Transforms a JSON string into an array.
     * Regular, the json is the content from the response.
     *
     * @param string $json The json string
     * @return array|null
     */
    abstract public function transformJsonResponse($json);

    /**
     * Requests projects at the Gerrit server
     *
     * @return array|null
     */
    abstract public function getProjects();

    /**
     * Requests changesets at the Gerrit server.
     *
     * @param string $projectName The project name
     * @param string $resumeKey The key where the request will be resumed
     * @throws \Exception
     */
    abstract public function getChangesets($projectName, $resumeKey = null);
}