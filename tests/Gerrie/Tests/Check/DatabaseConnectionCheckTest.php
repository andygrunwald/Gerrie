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

use Gerrie\Check\DatabaseConnectionCheck;

class DatabaseConnectionCheckTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Gerrie\Check\CheckInterface
     */
    protected $checkInstance;

    public function setUp()
    {
        $databaseMock = $this->getDatabaseMock();
        $this->checkInstance = new DatabaseConnectionCheck($databaseMock);
    }

    protected function getDatabaseMock($connectWillThrowException = false)
    {
        $database = $this->getMock('Gerrie\Component\Database\Database', [], [[]]);

        if ($connectWillThrowException === true) {
            $database->expects($this->any())
                     ->method('connect')
                     ->will($this->throwException(new \Exception()));
        }

        return $database;
    }

    public function tearDown()
    {
        $this->checkInstance = null;
    }

    public function testCheckWithDatabaseConnection()
    {
        $this->assertTrue($this->checkInstance->check());
    }

    public function testCheckWithoutDatabaseConnection()
    {
        $databaseMock = $this->getDatabaseMock(true);
        $checkInstance = new DatabaseConnectionCheck($databaseMock);

        $this->assertFalse($checkInstance->check());
    }

    public function testGetFailureMessage()
    {
        $this->checkInstance->check();
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