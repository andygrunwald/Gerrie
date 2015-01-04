<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Tests\API\DataService;

use Gerrie\API\DataService\SSHDataService;
use Gerrie\API\DataService\DataServiceInterface;

class SSHDataServiceTest extends DataServiceTestBase
{

    /**
     * @param array $configArgument
     * @return DataServiceInterface
     */
    protected function getServiceMock($configArgument = [])
    {
        $config = [
            'KeyFile' => '/Users/Dummy/.ssh/id_rsa_foo',
            'Port' => 29418
        ];
        $config = array_merge($config, $configArgument);

        $sshMock = $this->getMock('\Gerrie\Component\Connection\SSH', [], ['ssh', $config]);

        $instance = new SSHDataService($sshMock, $config);

        return $instance;
    }

    /**
     * TODO: Create tests for:
     *  * testSetterAndGetterQueryLimitWithoutInitialisation
     *  * transformJsonResponse
     *  * getProjects
     *  * getChangesets($projectName, $resumeKey = null, $start = 0);
     *  * getVersion
     */
}