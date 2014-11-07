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

use Gerrie\Check\PDOMySqlExtensionCheck;

class PDOMySqlExtensionCheckTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Gerrie\Check\CheckInterface
     */
    protected $checkInstance;

    public function setUp()
    {
        $this->checkInstance = new PDOMySqlExtensionCheck();
    }

    public function tearDown()
    {
        $this->checkInstance = null;
    }

    public function testCheck()
    {
        if (extension_loaded('PDO') === false || extension_loaded('pdo_mysql') === false) {
            $this->markTestSkipped('PHP Extensions "PDO" and "pdo_mysql" are required, but not available');
        }

       $this->assertTrue($this->checkInstance->check());
    }

    public function testGetFailureMessage()
    {
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