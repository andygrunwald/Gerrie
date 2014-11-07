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

use Gerrie\Component\Configuration\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Configuration
     */
    private $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration();
    }

    public function tearDown()
    {
        $this->configuration = null;
    }

    protected function getDummyConfig()
    {
        $dummyConfig = [
            'Foo' => 'Bar',
            'Bar' => [
                'Bar' => 'Baz',
                'Baz' => 'Gerrie',
                'Gerrie' => [
                    'Nested' => true
                ]
            ]
        ];

        return $dummyConfig;
    }

    public function testGetterConfigurationWithConstructor()
    {
        $dummyConfig = $this->getDummyConfig();
        $configuration = new Configuration($dummyConfig);

        $this->assertEquals('Bar', $configuration->getConfigurationValue('Foo'));
        $this->assertEquals($dummyConfig['Bar'], $configuration->getConfigurationValue('Bar'));

        $this->assertEquals('Baz', $configuration->getConfigurationValue('Bar.Bar'));
        $this->assertEquals('Gerrie', $configuration->getConfigurationValue('Bar.Baz'));
        $this->assertEquals('Gerrie', $configuration->getConfigurationValue('bar.baz'));
        $this->assertEquals('Gerrie', $configuration->getConfigurationValue('bar.Baz'));
        $this->assertEquals('Gerrie', $configuration->getConfigurationValue('Bar.baz'));

        $this->assertEquals($dummyConfig['Bar']['Gerrie'], $configuration->getConfigurationValue('Bar.Gerrie'));
        $this->assertEquals(true, $configuration->getConfigurationValue('Bar.Gerrie.Nested'));
    }

    public function testGetterConfigurationWithSetConfiguration()
    {
        $dummyConfig = $this->getDummyConfig();
        $this->configuration->setConfiguration($dummyConfig);

        $this->assertEquals('Bar', $this->configuration->getConfigurationValue('Foo'));
        $this->assertEquals($dummyConfig['Bar'], $this->configuration->getConfigurationValue('Bar'));

        $this->assertEquals('Baz', $this->configuration->getConfigurationValue('Bar.Bar'));
        $this->assertEquals('Gerrie', $this->configuration->getConfigurationValue('Bar.Baz'));
        $this->assertEquals('Gerrie', $this->configuration->getConfigurationValue('bar.baz'));
        $this->assertEquals('Gerrie', $this->configuration->getConfigurationValue('bar.Baz'));
        $this->assertEquals('Gerrie', $this->configuration->getConfigurationValue('Bar.baz'));

        $this->assertEquals($dummyConfig['Bar']['Gerrie'], $this->configuration->getConfigurationValue('Bar.Gerrie'));
        $this->assertEquals(true, $this->configuration->getConfigurationValue('Bar.Gerrie.Nested'));
    }

    public function testGetConfigurationWithConstructor()
    {
        $dummyConfig = $this->getDummyConfig();
        $this->configuration->setConfiguration($dummyConfig);

        $this->assertEquals($dummyConfig, $this->configuration->getConfiguration());
    }

    public function testGetConfigurationWithSetter()
    {
        $dummyConfig = $this->getDummyConfig();
        $configuration = new Configuration($dummyConfig);

        $this->assertEquals($dummyConfig, $configuration->getConfiguration());
    }

    public function testSetConfigurationValueWithEmptyConfiguration()
    {
        $this->assertEquals('', $this->configuration->getConfigurationValue('Foo'));
        $this->assertEquals('', $this->configuration->getConfigurationValue('Foo.Bar.Baz'));
        $this->assertEquals('', $this->configuration->getConfigurationValue('Foo.Gerrie'));
        $this->assertEquals('', $this->configuration->getConfigurationValue('Awesome.Application.YOLO'));

        $this->configuration->setConfigurationValue('Foo', 'Bar');
        $this->assertEquals('Bar', $this->configuration->getConfigurationValue('Foo'));

        $this->configuration->setConfigurationValue('Foo', 'Weekend');
        $this->assertEquals('Weekend', $this->configuration->getConfigurationValue('Foo'));

        $this->configuration->setConfigurationValue('Foo.Bar', 'Baz');
        $this->configuration->setConfigurationValue('Foo.Bar.Baz', 'Hello');
        $this->configuration->setConfigurationValue('Foo.Gerrie', 'World');
        $this->configuration->setConfigurationValue('Awesome.Application.YOLO', 'Google Google Google');

        $this->assertEquals(['Baz' => 'Hello'], $this->configuration->getConfigurationValue('Foo.Bar'));
        $this->assertEquals('Hello', $this->configuration->getConfigurationValue('Foo.Bar.Baz'));
        $this->assertEquals('World', $this->configuration->getConfigurationValue('Foo.Gerrie'));
        $this->assertEquals('Google Google Google', $this->configuration->getConfigurationValue('Awesome.Application.YOLO'));
    }
}