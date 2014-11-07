<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Tests\Component\Configuration;

use Gerrie\Component\Configuration\ConfigurationFactory;

class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{

    protected function getPathOfFixtureConfigFile()
    {
        $configFile  = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $configFile .= 'Fixture' . DIRECTORY_SEPARATOR . 'Config.yml';

        return $configFile;
    }

    /**
     * @expectedException     \RuntimeException
     * @expectedExceptionCode 1415381521
     */
    public function testGetConfigurationByConfigFileWithInvalidConfigFile()
    {
        ConfigurationFactory::getConfigurationByConfigFile('./doesNotExists.yml');
    }

    public function testGetConfigurationByConfigFileWithValidConfigFile()
    {
        $configFile = $this->getPathOfFixtureConfigFile();
        $configuration = ConfigurationFactory::getConfigurationByConfigFile($configFile);

        $this->assertInstanceOf('Gerrie\Component\Configuration\Configuration', $configuration);
        $this->assertEquals('root', $configuration->getConfigurationValue('Database.Username'));
    }

    public function testGetConfigurationByConfigFileAndCommandOptions()
    {
        $argvInputExtended = $this->getMock('Gerrie\Component\Console\ArgvInputExtended', ['isOptionSet', 'getOption']);
        $argvInputExtended->expects($this->any())
                          ->method('isOptionSet')
                          ->withConsecutive(
                              array($this->equalTo('database-host')),
                              array($this->equalTo('database-user')),
                              array($this->equalTo('database-pass')),
                              array($this->equalTo('database-port')),
                              array($this->equalTo('database-name'))
                          )
                          ->willReturnOnConsecutiveCalls(
                              $this->returnValue(true),
                              $this->returnValue(true),
                              $this->returnValue(false),
                              $this->returnValue(true),
                              $this->returnValue(false)
                          );

        $argvInputExtended->expects($this->any())
                          ->method('getOption')
                          ->withConsecutive(
                              array($this->equalTo('database-host')),
                              array($this->equalTo('database-user')),
                              //array($this->equalTo('database-pass')),
                              array($this->equalTo('database-port'))
                              //array($this->equalTo('database-name'))
                          )
                          ->willReturnOnConsecutiveCalls(
                              $this->returnValue('HOST'),
                              $this->returnValue('USER'),
                              //$this->returnValue('PASS'),
                              $this->returnValue('PORT')
                              //$this->returnValue('NAME')
                          );

        $configFile = $this->getPathOfFixtureConfigFile();
        $configuration = ConfigurationFactory::getConfigurationByConfigFileAndCommandOptions($configFile, $argvInputExtended);

        $this->assertInstanceOf('Gerrie\Component\Configuration\Configuration', $configuration);

        $this->assertEquals('HOST', $configuration->getConfigurationValue('Database.Host'));
        $this->assertEquals('USER', $configuration->getConfigurationValue('Database.Username'));
        $this->assertEquals(null, $configuration->getConfigurationValue('Database.Password'));
        $this->assertEquals('PORT', $configuration->getConfigurationValue('Database.Port'));
        $this->assertEquals('gerrie', $configuration->getConfigurationValue('Database.Name'));
    }
}