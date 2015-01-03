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

use Gerrie\Component\Connection\SSH;
use Buzz\Client\Curl;
use Buzz\Browser;
use Buzz\Listener\BasicAuthListener;

class DataServiceFactory
{

    /**
     * Main factory method to get a data service based on the incoming instance config.
     *
     * $instanceConfig is an array in the structure:
     * [
     *  'Instance' => [
     *      'scheme' => "ssh",
     *      'host' => "review.typo3.org",
     *      'port' => 29418,
     *      'user' => "max.mustermann",
     *      ...
     *  ],
     *  'KeyFile' => null | /Path/To/Key/File
     * ]
     *
     * The KeyFile attribute is not needed for the HTTP data service.
     *
     * @param array $instanceConfig
     * @return HTTPDataService|SSHDataService
     * @throws \RuntimeException
     */
    public static function getDataService(array $instanceConfig)
    {
        $instanceDetails = parse_url($instanceConfig['Instance']);

        if (isset($instanceDetails['host']) === false || isset($instanceDetails['scheme']) === false) {
            $exceptionMessage = sprintf('Error while parsing instance configuration "%s"', $instanceConfig['Instance']);
            throw new \RuntimeException($exceptionMessage, 1415453791);
        }

        $instanceConfig['Instance'] = $instanceDetails;

        $scheme = strtoupper($instanceConfig['Instance']['scheme']);
        switch ($scheme) {
            case 'SSH':
                $dataService = static::bootstrapSSHDataService($instanceConfig);
                break;
            case 'HTTPS':
            case 'HTTP':
                unset($instanceConfig['KeyFile']);
                $dataService = static::bootstrapHTTPDataService($instanceConfig);
                break;
            default:
                $exceptionMessage = sprintf('Data service for scheme "%s" is not available', $scheme);
                throw new \RuntimeException($exceptionMessage, 1364130057);
        }

        return $dataService;
    }

    /**
     * Bootstraps a data service for the Gerrit SSH API.
     *
     * For $instanceConfig documentation see self::getDataService.
     *
     * @param array $instanceConfig
     * @return SSHDataService
     */
    protected static function bootstrapSSHDataService(array $instanceConfig)
    {
        // TODO: This is a little bit strange that i have to put KeyFile and Port into the SSH adapter
        //       I think this has to be refactored.
        //       All SSH credentials have to be in one place
        $sshConfig = [
            'KeyFile' => $instanceConfig['KeyFile'],
            'Port' => $instanceConfig['Instance']['port']
        ];
        $ssh = new SSH('ssh', $sshConfig);
        $dataService = new SSHDataService($ssh, $instanceConfig['Instance']);

        return $dataService;
    }

    /**
     * Bootstraps a data service for the Gerrit REST / HTTP API.
     *
     * For $instanceConfig documentation see self::getDataService.
     *
     * @param array $instanceConfig
     * @return HTTPDataService
     */
    protected static function bootstrapHTTPDataService(array $instanceConfig)
    {
        $restClient = static::getHTTPClientInstance($instanceConfig);
        $dataService = new HTTPDataService($restClient, $instanceConfig['Instance']);

        return $dataService;
    }

    /**
     * Creates the HTTP abstraction object hierarchy.
     * For this purpose we use an external library called "Buzz".
     *
     * For $instanceConfig documentation see self::getDataService.
     *
     * @param array $instanceConfig
     * @return Browser
     */
    protected static function getHTTPClientInstance(array $instanceConfig)
    {
        $username = ((isset($instanceConfig['Instance']['user']) === true) ? $instanceConfig['Instance']['user'] : '');
        $password = ((isset($instanceConfig['Instance']['pass']) === true) ? $instanceConfig['Instance']['pass'] : '');

        // Bootstrap the REST client
        $curlClient = new Curl();
        $curlClient->setVerifyPeer(false);
        $restClient = new Browser($curlClient);

        if ($username && $password) {
            $authListener = new BasicAuthListener($username, $password);
            $restClient->addListener($authListener);
        }

        return $restClient;
    }
}