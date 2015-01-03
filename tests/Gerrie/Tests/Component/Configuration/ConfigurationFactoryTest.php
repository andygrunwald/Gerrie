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

    protected function getFixtureConfigFilePath()
    {
        $configFile  = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $configFile .= 'Fixture' . DIRECTORY_SEPARATOR . 'DummyConfig.yml';
        $configFile = realpath($configFile);

        return $configFile;
    }

    protected function getDummyInstances()
    {
        $instances = [
            'ssh://max.mustermann@review.typo3.org:29418/',
            'https://max.mustermann:password@gerrit.wikimedia.org/r/'
        ];

        return $instances;
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
        $configFile = $this->getFixtureConfigFilePath();
        $configuration = ConfigurationFactory::getConfigurationByConfigFile($configFile);

        $this->assertInstanceOf('Gerrie\Component\Configuration\Configuration', $configuration);
        $this->assertEquals('root', $configuration->getConfigurationValue('Database.Username'));
    }

    protected function getArgvInputExtendedMockObject($withConfigFileOption, $withInstancesArguments = false, $dummyInstances = false)
    {
        $mockedMethods = ['isOptionSet', 'getOption', 'getArgument', 'hasArgument'];
        $argvInputExtended = $this->getMock('Gerrie\Component\Console\ArgvInputExtended', $mockedMethods);
        $argvInputExtended->expects($this->any())
            ->method('isOptionSet')
            ->withConsecutive(
                array($this->equalTo('config-file')),
                array($this->equalTo('database-host')),
                array($this->equalTo('database-user')),
                array($this->equalTo('database-pass')),
                array($this->equalTo('database-port')),
                array($this->equalTo('database-name'))
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue($withConfigFileOption),
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
                array($this->equalTo('database-port'))
            )
            ->willReturnOnConsecutiveCalls(
                $this->returnValue('HOST'),
                $this->returnValue('USER'),
                $this->returnValue('PORT')
            );

        $argvInputExtended->expects($this->any())
            ->method('hasArgument')
            ->with($this->equalTo('instances'))
            ->will($this->returnValue($withInstancesArguments));

        if ($withInstancesArguments === true && $dummyInstances === true) {
            $instances = $this->getDummyInstances();
            $argvInputExtended->expects($this->any())
                ->method('getArgument')
                ->with($this->equalTo('instances'))
                ->will($this->returnValue($instances));
        }
        /*
        if ($withInstancesArguments === false) {





        } else {
            $argvInputExtended->expects($this->any())
                ->method('hasArgument')
                ->with($this->equalTo('instances'))
                ->will($this->returnValue(true));
        }
*/
        return $argvInputExtended;
    }

    public function testGetConfigurationByConfigFileAndCommandOptionsAndArguments()
    {
        $argvInputExtended = $this->getArgvInputExtendedMockObject(true);

        $configFile = $this->getFixtureConfigFilePath();
        $configuration = ConfigurationFactory::getConfigurationByConfigFileAndCommandOptionsAndArguments($configFile, $argvInputExtended);

        $this->assertInstanceOf('Gerrie\Component\Configuration\Configuration', $configuration);

        $this->assertEquals('HOST', $configuration->getConfigurationValue('Database.Host'));
        $this->assertEquals('USER', $configuration->getConfigurationValue('Database.Username'));
        $this->assertEquals(null, $configuration->getConfigurationValue('Database.Password'));
        $this->assertEquals('PORT', $configuration->getConfigurationValue('Database.Port'));
        $this->assertEquals('gerrie', $configuration->getConfigurationValue('Database.Name'));
    }

    public function testGetConfigurationByConfigFileAndCommandOptionsAndArgumentsWithoutConfigFileOption()
    {
        $argvInputExtended = $this->getArgvInputExtendedMockObject(false);

        $configFile = $this->getFixtureConfigFilePath();
        $configuration = ConfigurationFactory::getConfigurationByConfigFileAndCommandOptionsAndArguments($configFile, $argvInputExtended);

        $this->assertInstanceOf('Gerrie\Component\Configuration\Configuration', $configuration);

        $this->assertEquals('HOST', $configuration->getConfigurationValue('Database.Host'));
        $this->assertEquals('USER', $configuration->getConfigurationValue('Database.Username'));
        $this->assertEquals(null, $configuration->getConfigurationValue('Database.Password'));
        $this->assertEquals('PORT', $configuration->getConfigurationValue('Database.Port'));
    }

    public function testGetConfigurationByConfigFileAndCommandOptionsAndArgumentsWitZeroInstanceArgument()
    {
        $argvInputExtended = $this->getArgvInputExtendedMockObject(false, true);

        $configFile = $this->getFixtureConfigFilePath();
        $configuration = ConfigurationFactory::getConfigurationByConfigFileAndCommandOptionsAndArguments($configFile, $argvInputExtended);

        $this->assertInstanceOf('Gerrie\Component\Configuration\Configuration', $configuration);

        $this->assertEquals('HOST', $configuration->getConfigurationValue('Database.Host'));
        $this->assertEquals('USER', $configuration->getConfigurationValue('Database.Username'));
        $this->assertEquals(null, $configuration->getConfigurationValue('Database.Password'));
        $this->assertEquals('PORT', $configuration->getConfigurationValue('Database.Port'));
        $this->assertEquals(null, $configuration->getConfigurationValue('Gerrit.Gerrie'));
    }

    public function testGetConfigurationByConfigFileAndCommandOptionsAndArgumentsWitDummyInstanceArgument()
    {
        $argvInputExtended = $this->getArgvInputExtendedMockObject(false, true, true);

        $configFile = $this->getFixtureConfigFilePath();
        $configuration = ConfigurationFactory::getConfigurationByConfigFileAndCommandOptionsAndArguments($configFile, $argvInputExtended);

        $this->assertInstanceOf('Gerrie\Component\Configuration\Configuration', $configuration);

        $this->assertEquals('HOST', $configuration->getConfigurationValue('Database.Host'));
        $this->assertEquals('USER', $configuration->getConfigurationValue('Database.Username'));
        $this->assertEquals(null, $configuration->getConfigurationValue('Database.Password'));
        $this->assertEquals('PORT', $configuration->getConfigurationValue('Database.Port'));
        $this->assertInternalType('array', $configuration->getConfigurationValue('Gerrit.Gerrie'));

        $instances = $this->getDummyInstances();
        $this->assertEquals($instances, $configuration->getConfigurationValue('Gerrit.Gerrie'));
    }
}