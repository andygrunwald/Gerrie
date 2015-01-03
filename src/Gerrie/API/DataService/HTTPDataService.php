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

class HTTPDataService extends BaseDataService
{

    /**
     * Constructor
     *
     * @param \Buzz\Browser $connector
     * @param array $config
     * @return \Gerrie\API\DataService\HTTPDataService
     */
    public function __construct(\Buzz\Browser $connector, array $config)
    {
        $this->setConnector($connector);
        $this->setConfig($config);
        $this->setName('HTTP');
    }

    /**
     * Gets the base url for all HTTP requests.
     *
     * @param bool $withAuthentication If true, the authentification string will be appended. False otherwise
     * @return string
     */
    protected function getBaseUrl($withAuthentication = false)
    {
        $config = $this->getConfig();
        $baseUrl = $config['scheme'] . '://' . rtrim($config['host'], '/') . '/';

        if (isset($config['path'])) {
            $baseUrl .= trim($config['path'], '/') . '/';
        }

        if ($withAuthentication === true
            && $this->getConnector()->getListener() instanceof \Buzz\Listener\BasicAuthListener) {

            $baseUrl .= 'a/';
        }

        return $baseUrl;
    }

    /**
     * Verifies the last request.
     * If the last request was not successful, it will be throw an exception.
     *
     * @param \Buzz\Message\Response $response The response object from the last reques
     * @param string $url The url which was requested
     * @return \Buzz\Message\Response
     * @throws \Exception
     */
    protected function verifyResult(\Buzz\Message\Response $response, $url)
    {
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Request to "' . $url . '" failed', 1364061673);
        }

        return $response;
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
        // In a REST-API call, the first five chars are )]}'\n
        // to decode it, we have to strip it
        // See https://review.typo3.org/Documentation/rest-api.html#output
        if (substr($json, 0, 4) === ')]}\'') {
            $json = substr($json, 5);
        }

        return json_decode($json, true);
    }

    /**
     * Initiales the query limit
     *
     * @return int
     */
    protected function initQueryLimit()
    {
        $url = $this->getBaseUrl(true) . 'accounts/self/capabilities?format=JSON';
        $response = $this->getConnector()->get($url);
        $response = $this->verifyResult($response, $url);

        $content = $this->transformJsonResponse($response->getContent());

        return $content['queryLimit']['max'];
    }

    /**
     * Gets the Host
     *
     * @return string
     */
    public function getHost()
    {
        $config = $this->getConfig();

        return $config['host'];
    }

    /**
     * Requests projects at the Gerrit server
     *
     * @return array|null
     */
    public function getProjects()
    {
        $urlParts = array(
            'format' => 'JSON',
            'description' => '',
            'type' => 'all',
            'all' => '',
            'tree' => '',
        );

        $url = $this->getBaseUrl() . 'projects/?' . http_build_query($urlParts);

        $response = $this->getConnector()->get($url);
        $response = $this->verifyResult($response, $url);

        $content = $this->transformJsonResponse($response->getContent());

        return $content;
    }

    /**
     * Requests changesets at the Gerrit server.
     *
     * This method is not implemented yet, because at the moment (2013-03-24) Gerrit 2.6.* is not released.
     * Many Gerrit systems (e.g. TYPO3, WikiMedia, OpenStack, etc.) are running at 2.5.*.
     * In 2.5.* the SSH API delivers more information than the REST API.
     *
     * If Gerrit 2.6 is released, the HTTP DataService will be extended and fully implemented.
     * Maybe, you want to help me?
     *
     * SSH commands:
     * /usr/bin/ssh -p 29418 review.typo3.org gerrit query --format 'JSON' --current-patch-set
     *                                                     --all-approvals --files --comments
     *                                                     --commit-message --dependencies --submit-records
     *                                                     'project:Documentation/ApiTypo3Org' limit:'500' 2>&1
     * /usr/bin/ssh -p 29418 review.typo3.org gerrit query --format 'JSON' --current-patch-set
     *                                                     --all-approvals --files --comments
     *                                                     --commit-message --dependencies --submit-records
     *                                                     'project:Documentation/ApiTypo3Org' limit:'500'
     *                                                     resume_sortkey:'00215ec7000041b3' 2>&1
     *
     * @param string $projectName The project name
     * @param string $resumeKey The key where the request will be resumed
     * @param integer $start Number of changesets which will be skipped
     * @return array|null
     * @throws \Exception
     */
    public function getChangesets($projectName, $resumeKey = null, $start = 0)
    {
        throw new \Exception(__METHOD__ . ' not implemented yet. Will you help me?', 1374257295);

        $urlParts = array(
            'q' => sprintf('project:%s', $projectName),
            'n' => $this->getQueryLimit()
        );

        // The "o" parameter can be applied more than one time
        // This parameter defines how detailed the answer will be
        $oOptions = array(
            'LABELS',
            'DETAILED_LABELS',
            'CURRENT_REVISION',
            'ALL_REVISIONS',
            'CURRENT_COMMIT',
            'ALL_COMMITS',
            'CURRENT_FILES',
            'ALL_FILES',
            'DETAILED_ACCOUNTS',
            'MESSAGES'
        );
        $additionalFields = $this->buildQueryStringWithSameParameterName('o', $oOptions);
        $url = $this->getBaseUrl() . 'changes/?' . http_build_query($urlParts) . '&' . $additionalFields;

        $response = $this->getConnector()->get($url);
        $response = $this->verifyResult($response, $url);

        $content = $this->transformJsonResponse($response->getContent());

        return $content;
    }

    /**
     * This function build a url query string with a parameter which can be applied more than one time.
     * E.g. http://www.google.de/?q=5&q=6&q=7&q=8&q=9...
     *
     * This method is used to apply the parameter "o" in GET /changes/ command for REST-API.
     *
     * @see https://review.typo3.org/Documentation/rest-api-changes.html#list-changes
     *
     * @param string $parameterName Parametername which should be used more than one time
     * @param array $values Various of values
     * @return string
     */
    private function buildQueryStringWithSameParameterName($parameterName, array $values)
    {
        $queryString = '';

        foreach ($values as $value) {
            if ($queryString) {
                $queryString .= '&';
            }

            $queryString .= http_build_query(array($parameterName => $value));
        }

        return $queryString;
    }

    /**
     * Returns the version of the Gerrit server
     *
     * @link https://review.typo3.org/Documentation/rest-api-config.html
     *
     * @return string
     */
    public function getVersion()
    {
        $url = $this->getBaseUrl() . 'config/server/version';
        $response = $this->getConnector()->get($url);
        $response = $this->verifyResult($response, $url);

        $content = $this->transformJsonResponse($response->getContent());

        return $content;
    }
}