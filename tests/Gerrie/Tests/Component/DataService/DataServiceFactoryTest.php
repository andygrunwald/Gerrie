<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Tests\Component\DataService;

use Gerrie\Component\DataService\DataServiceFactory;

class DataServiceFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function invalidInstancePathDataProvider()
    {
        $data = [
            array(['Instance' => null]),
            array(['Instance' => '']),
            array(['Instance' => '/foo/bar']),
            array(['Instance' => 'www.google.de/path/x'])
        ];

        return $data;
    }

    public function invalidDataServiceSchemeDataProvider()
    {
        $data = [
            array(['Instance' => 'google://my.website/']),
            array(['Instance' => 'ftp://192.168.1.1/']),
            array(['Instance' => 'sftp://ccc.de/'])
        ];

        return $data;
    }

    /**
     * @dataProvider invalidInstancePathDataProvider
     * @expectedException     \RuntimeException
     * @expectedExceptionCode 1415453791
     */
    public function testGetDataServiceWithInvalidInstancePath($instanceConfig)
    {
        DataServiceFactory::getDataService($instanceConfig);
    }

    /**
     * @dataProvider invalidDataServiceSchemeDataProvider
     * @expectedException     \RuntimeException
     * @expectedExceptionCode 1364130057
     */
    public function testGetDataServiceWithInvalidDataServiceScheme($instanceConfig)
    {
        DataServiceFactory::getDataService($instanceConfig);
    }

    public function testGetSSHDataService()
    {
        $instanceConfig = [
            'Instance' => 'ssh://max.mustermann@review.typo3.org:29418/',
            'KeyFile' => '',
        ];
        $sshDataService = DataServiceFactory::getDataService($instanceConfig);

        $this->assertInstanceOf('Gerrie\Component\DataService\SSHDataService', $sshDataService);
    }

    public function testGetHTTPDataService()
    {
        $instanceConfig = [
            'Instance' => 'http://andy.grunwald:pass@review.typo3.org:80/'
        ];
        $httpDataService = DataServiceFactory::getDataService($instanceConfig);

        $this->assertInstanceOf('Gerrie\Component\DataService\HTTPDataService', $httpDataService);
    }

    public function testGetHTTPSDataService()
    {
        $instanceConfig = [
            'Instance' => 'https://andy.grunwald:pass@review.typo3.org:80/'
        ];
        $httpDataService = DataServiceFactory::getDataService($instanceConfig);

        $this->assertInstanceOf('Gerrie\Component\DataService\HTTPDataService', $httpDataService);
    }
}