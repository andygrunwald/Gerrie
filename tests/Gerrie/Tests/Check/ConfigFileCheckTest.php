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

use Gerrie\Check\ConfigFileCheck;

class ConfigFileCheckTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Gerrie\Check\CheckInterface
     */
    protected $checkInstance;

    public function setUp()
    {
        $this->checkInstance = new ConfigFileCheck($this->getPathOfFixtureConfigFile());
    }

    public function tearDown()
    {
        $this->checkInstance = null;
    }

    protected function getPathOfFixtureConfigFile()
    {
        $configFile  = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $configFile .= 'Fixture' . DIRECTORY_SEPARATOR . 'DummyConfig.yml';
        $configFile = realpath($configFile);

        return $configFile;
    }

    public function testCheck()
    {
        $this->assertTrue($this->checkInstance->check());
    }

    public function testWithNotExistingConfigCheck()
    {
        $checkInstance = new ConfigFileCheck('');
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