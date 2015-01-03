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

class SSHDataServiceTest extends DataServiceTestBase
{

    protected function getServiceMock() {
        $config = [
            'KeyFile' => '/Users/Dummy/.ssh/id_rsa_foo',
            'Port' => 29418
        ];
        $sshMock = $this->getMock('\Gerrie\Component\Connection\SSH', [], ['ssh', $config]);

        $instance = new SSHDataService($sshMock, $config);

        return $instance;
    }
}