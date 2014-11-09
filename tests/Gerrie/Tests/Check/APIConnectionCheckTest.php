<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Tests\Check;

use Gerrie\Check\APIConnectionCheck;

class APIConnectionCheckTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gerrie\Check\CheckInterface
     */
    protected $checkInstance;

    public function setUp()
    {
        $apiConnectionMock = $this->getDataServiceMock();
        $this->checkInstance = new APIConnectionCheck($apiConnectionMock);
    }

    public function tearDown()
    {
        $this->checkInstance = null;
    }

    protected function getDataServiceMock($getVersionReturnValue = '1.2.3')
    {
        $apiConnectionMock = $this->getMock('Gerrie\Component\DataService\DataServiceInterface');
        $apiConnectionMock->expects($this->once())
                          ->method('getVersion')
                          ->will($this->returnValue($getVersionReturnValue));

        return $apiConnectionMock;
    }

    public function testCheckWithVersion()
    {
       $this->assertTrue($this->checkInstance->check());
    }

    public function testCheckWithoutVersion()
    {
        $apiConnectionMock = $this->getDataServiceMock('');
        $checkInstance = new APIConnectionCheck($apiConnectionMock);

        $this->assertFalse($checkInstance->check());
    }

    public function testGetFailureMessage()
    {
        $this->assertInternalType('string', $this->checkInstance->getFailureMessage());
    }

    public function testGetSuccessMessage()
    {
        $this->assertInternalType('string', $this->checkInstance->getSuccessMessage());
    }

    public function testIsOptional()
    {
        $this->assertInternalType('bool', $this->checkInstance->isOptional());
    }
}