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

    public function testGetterAndSetterName()
    {
        $instance = $this->getServiceMock();

        $name = 'UnitTest Test name';

        $this->assertNotEmpty($instance->getName());
        $instance->setName($name);
        $this->assertEquals($name, $instance->getName());
    }

    public function testGetterAndSetterConnector()
    {
        $instance = $this->getServiceMock();

        $dummyConnector = new \stdClass();

        $instance->setConnector($dummyConnector);
        $this->assertEquals($dummyConnector, $instance->getConnector());
    }

    public function testGetterAndSetterConfig()
    {
        $instance = $this->getServiceMock();

        $dummyConfig = [
            'key' => 'value',
            'hash' => [
                'my' => 'map',
                'Gerrit' => 'Gerrie',
                'HTTP' => 'SSH'
            ]
        ];

        $instance->setConfig($dummyConfig);
        $this->assertEquals($dummyConfig, $instance->getConfig());
    }

    public function testGetHost()
    {
        $dummyHost = 'example.com';
        $instance = $this->getServiceMock(['host' => $dummyHost]);

        $this->assertEquals($dummyHost, $instance->getHost());
    }

    public function testSetterAndGetterQueryLimitWithoutInitialisation()
    {
        $instance = $this->getServiceMock();

        $queryLimit = 1234;
        $instance->setQueryLimit($queryLimit);

        $this->assertInternalType('int', $instance->getQueryLimit());
        $this->assertEquals($queryLimit, $instance->getQueryLimit());
    }

    public function testTransformJsonResponseWithValidJson()
    {
        $json = '{"count":5,"stringVal":"fooBar","smallArray":[5,3,1]}';

        $jsonResult = [
            'count' => 5,
            'stringVal' => "fooBar",
            'smallArray' => [
                5,
                3,
                1
            ]
        ];

        $instance = $this->getServiceMock();
        $response = $instance->transformJsonResponse($json);

        $this->assertInternalType('array', $response);
        $this->assertEquals($jsonResult, $response);
    }

    public function testTransformJsonResponseWithInvalidJson()
    {
        $json = '{"count":5 "stringVal":"fooBar","smallArray":[5,3,1]}';

        $instance = $this->getServiceMock();
        $response = $instance->transformJsonResponse($json);

        $this->assertNull($response);
    }
}