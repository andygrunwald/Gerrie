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

use Gerrie\Check\ConfigurationValidationCheck;

class ConfigFileValidationCheckTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Gerrie\Check\CheckInterface
     */
    protected $checkInstance;

    public function setUp()
    {
        $configuration = $this->getConfigurationMock();
        $this->checkInstance = new ConfigurationValidationCheck($configuration);
    }

    public function tearDown()
    {
        $this->checkInstance = null;
    }

    public function getConfigurationMock($allTrue = false)
    {
        $configuration = $this->getMock('Gerrie\Component\Configuration\Configuration', ['hasConfigurationKey']);
        $configuration->expects($this->any())
            ->method('hasConfigurationKey')
            ->withConsecutive(
                array($this->equalTo('Database.Host')),
                array($this->equalTo('Database.Username')),
                array($this->equalTo('Database.Password')),
                array($this->equalTo('Database.Port')),
                array($this->equalTo('Database.Name'))
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue(true),
                $this->returnValue(true),
                $this->returnValue($allTrue),
                $this->returnValue(true),
                $this->returnValue($allTrue)
            );

        return $configuration;
    }

    public function testCheckWithAllProperties()
    {
        $configuration = $this->getConfigurationMock(true);
        $configFileValidationCheck = new ConfigurationValidationCheck($configuration);
        $this->assertTrue($configFileValidationCheck->check());
    }

    public function testCheckWithMissingProperties()
    {
        $this->assertFalse($this->checkInstance->check());
    }

    /*
    public function testWithNotExistingConfigCheck()
    {
        $checkInstance = new ConfigFileCheck('');
        $this->assertFalse($checkInstance->check());
    }
    */

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