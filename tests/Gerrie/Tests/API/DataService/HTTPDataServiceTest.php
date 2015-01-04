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

use Gerrie\API\DataService\HTTPDataService;
use Gerrie\API\DataService\DataServiceInterface;

class HTTPDataServiceTest extends DataServiceTestBase
{

    /**
     * @param array $configArgument
     * @return DataServiceInterface
     */
    protected function getServiceMock($configArgument = [])
    {
        $config = [];
        $config = array_merge($config, $configArgument);

        $buzzMock = $this->getMock('\Buzz\Browser');

        $instance = new HTTPDataService($buzzMock, $config);

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