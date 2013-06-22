<?php

namespace Gerrie\Tests\Helper;

use Gerrie\Helper\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase {

    public function testGetHTTPClientInstanceWithoutCredentials() {
        $httpClient = Factory::getHTTPClientInstance(array());

        $this->assertInstanceOf('\Buzz\Browser', $httpClient);
        $this->assertInstanceOf('\Buzz\Client\Curl', $httpClient->getClient());
    }
}