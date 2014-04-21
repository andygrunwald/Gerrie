<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Tests\Helper;

use Gerrie\Helper\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testGetHTTPClientInstanceWithoutCredentials()
    {
        $httpClient = Factory::getHTTPClientInstance(array());

        $this->assertInstanceOf('\Buzz\Browser', $httpClient);
        $this->assertInstanceOf('\Buzz\Client\Curl', $httpClient->getClient());
    }
}