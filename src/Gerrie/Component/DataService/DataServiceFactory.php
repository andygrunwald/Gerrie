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

class DataServiceFactory
{

    public static function getHTTPClientInstance(array $config)
    {
        $username = ((isset($config['HTTP']['Username']) === true) ? $config['HTTP']['Username'] : '');
        $password = ((isset($config['HTTP']['Password']) === true) ? $config['HTTP']['Password'] : '');

        // Bootstrap the rest client
        $curlClient = new \Buzz\Client\Curl();
        $curlClient->setVerifyPeer(false);
        $restClient = new \Buzz\Browser($curlClient);

        if ($username && $password) {
            $authListener = new \Buzz\Listener\BasicAuthListener($username, $password);
            $restClient->addListener($authListener);
        }

        return $restClient;
    }

    public static function getDataService(Configuration $config, $projectName)
    {
        $projectConfig = $config->getConfigurationValue('Gerrit.' . $projectName);
        $dataServiceConfig = strtoupper($projectConfig['DataService']);

        switch ($dataServiceConfig) {
            case 'SSH':
                $dataService = static::bootstrapSSHDataService($config, $projectConfig);
                break;
            case 'HTTP':
                $dataService = static::bootstrapHTTPDataService($projectConfig);
                break;
            default:
                $exceptionMessage = sprintf('Data service "%s" not supported', $dataServiceConfig);
                throw new \Exception($exceptionMessage, 1364130057);
        }

        return $dataService;
    }

    public static function bootstrapSSHDataService(Configuration $config, array $projectConfig)
    {
        $sshExec = $config->getConfigurationValue('Executable.SSH');
        $ssh = new SSH($sshExec, $projectConfig['SSH']);

        $dataService = new \Gerrie\Component\DataService\SSH($ssh, $projectConfig['SSH']);

        return $dataService;
    }

    public static function bootstrapHTTPDataService(array $projectConfig)
    {
        $restClient = static::getHTTPClientInstance($projectConfig);
        $dataService = new \Gerrie\Component\DataService\HTTPDataService($restClient, $projectConfig['HTTP']);

        return $dataService;
    }
}