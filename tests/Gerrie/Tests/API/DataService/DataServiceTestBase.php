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

class DataServiceTestBase extends \PHPUnit_Framework_TestCase
{

    public function testGetAndSetName() {
        $instance = $this->getServiceMock();

        $name = 'UnitTest Test name';

        $this->assertNotEmpty($instance->getName());
        $instance->setName($name);
        $this->assertEquals($name, $instance->getName());
    }
}