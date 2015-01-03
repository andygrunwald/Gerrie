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

class HTTPDataServiceTest extends DataServiceTestBase
{

    protected function getServiceMock()
    {
        $config = [];
        $buzzMock = $this->getMock('\Buzz\Browser');

        $instance = new HTTPDataService($buzzMock, $config);

        return $instance;
    }
}