<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Tests\Component\Database;

use Gerrie\Component\Database\Database;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{

    protected function getDatabaseInstance()
    {
        $config = [];
        $connectImmediately = false;

        $instance = new Database($config, $connectImmediately);

        return $instance;
    }

    public function testConfigGetterAndSetter()
    {
        $instance = $this->getDatabaseInstance();
        $testConfig = [
            'key' => 'value',
            'host' => '127.0.0.1',
            'user' => 'root'
        ];

        $this->assertEquals([], $instance->getConfig());
        $instance->setConfig($testConfig);
        $this->assertEquals($testConfig, $instance->getConfig());
    }

    public function testGetTableDefinition()
    {
        $instance = $this->getDatabaseInstance();
        $tableDefinition = $instance->getTableDefinition();

        $this->assertInternalType('array', $tableDefinition);
        $this->assertTrue(count($tableDefinition) > 0);
    }
}