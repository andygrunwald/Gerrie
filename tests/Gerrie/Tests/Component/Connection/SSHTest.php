<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Tests\Component\Connection;

use Gerrie\Component\Connection\SSH;

class SSHTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return SSH
     */
    public function getSSHInstance($executable)
    {
        $config = [
            'KeyFile' => '',
            'Port' => 0
        ];

        $sshInstance = new SSH($executable, $config);

        return $sshInstance;
    }

    public function testGetterAndSetterExecutable()
    {
        $instance = $this->getSSHInstance('');
        $executable = '/usr/local/bin/ssh';

        $this->assertEmpty($instance->getExecutable());

        $instance->setExecutable($executable);
        $this->assertEquals($executable, $instance->getExecutable());
    }

    public function testGetterAndSetterKeyFile()
    {
        $instance = $this->getSSHInstance('');
        $sshKeyFile = '/Users/max/.ssh/id_rsa_unittest';

        $this->assertEmpty($instance->getKeyFile());

        $instance->setKeyFile($sshKeyFile);
        $this->assertEquals($sshKeyFile, $instance->getKeyFile());
    }

    public function testGetterAndSetterWithValidPort()
    {
        $instance = $this->getSSHInstance('');
        $port = 12345;

        $this->assertEquals(0, $instance->getPort());

        $instance->setPort($port);
        $this->assertEquals($port, $instance->getPort());
    }

    public function testGetterAndSetterWithInvalidPort()
    {
        $instance = $this->getSSHInstance('');
        $port = 'FooBar';

        $this->assertEquals(0, $instance->getPort());

        $instance->setPort($port);
        $this->assertEquals(0, $instance->getPort());
    }

    public function testAddCommandParts()
    {
        $instance = $this->getSSHInstance('');
        $commandParts = [
            'ls-project',
            'example.com'
        ];

        $this->assertEquals([], $instance->getCommandParts());

        $instance->addCommandPart($commandParts[0]);
        $instance->addCommandPart($commandParts[1]);

        $this->assertEquals($commandParts, $instance->getCommandParts());
    }

    public function testAddArguments()
    {
        $instance = $this->getSSHInstance('');
        $arguments = [
            ['argument', 'value', '='],
            ['--branch', '"\'fo', ' ']
        ];
        $argumentsResult = [
            0 => "argument='value'",
            1 => "--branch '\"'\''fo'"
        ];

        $this->assertEquals([], $instance->getArguments());

        $instance->addArgument($arguments[0][0], $arguments[0][1], $arguments[0][2]);
        $instance->addArgument($arguments[1][0], $arguments[1][1], $arguments[1][2]);

        $this->assertEquals($argumentsResult, $instance->getArguments());
    }

    public function testResetArgumentsAndCommandParts()
    {
        $instance = $this->getSSHInstance('');
        $commandParts = [
            'ls-project',
            'example.com'
        ];
        $arguments = [
            ['argument', 'value', '='],
            ['--branch', '"\'fo', ' ']
        ];

        $this->assertEquals([], $instance->getCommandParts());
        $this->assertEquals([], $instance->getArguments());

        $instance->addCommandPart($commandParts[0]);
        $instance->addCommandPart($commandParts[1]);

        $instance->addArgument($arguments[0][0], $arguments[0][1], $arguments[0][2]);
        $instance->addArgument($arguments[1][0], $arguments[1][1], $arguments[1][2]);

        $this->assertCount(2, $instance->getCommandParts());
        $this->assertCount(2, $instance->getArguments());

        $instance->reset();

        $this->assertCount(0, $instance->getCommandParts());
        $this->assertCount(0, $instance->getArguments());
        $this->assertInternalType('array', $instance->getCommandParts());
        $this->assertInternalType('array', $instance->getArguments());
    }

    public function testGetEmptyCommand()
    {
        $instance = $this->getSSHInstance('');

        $this->assertEquals('', $instance->getCommand());
    }

    public function testGetCommand()
    {
        $instance = $this->getSSHInstance('/usr/bin/ssh');

        $commandParts = [
            'ls-project'
        ];
        $arguments = [
            ['argument', 'value', '='],
            ['--branch', '"\'fo', ' ']
        ];

        $instance->addCommandPart($commandParts[0]);

        $instance->addArgument($arguments[0][0], $arguments[0][1], $arguments[0][2]);
        $instance->addArgument($arguments[1][0], $arguments[1][1], $arguments[1][2]);

        $command = "/usr/bin/ssh ls-project argument='value' --branch '\"'\''fo' 2>&1";
        $this->assertEquals($command, $instance->getCommand());
    }

    public function testGetCommandWithKeyFile()
    {
        $instance = $this->getSSHInstance('/usr/bin/ssh');
        $instance->setKeyFile('/Users/max/.ssh/id_rsa');

        $commandParts = [
            'ls-project'
        ];
        $arguments = [
            ['argument', 'value', '='],
            ['--branch', '"\'fo', ' ']
        ];

        $instance->addCommandPart($commandParts[0]);

        $instance->addArgument($arguments[0][0], $arguments[0][1], $arguments[0][2]);
        $instance->addArgument($arguments[1][0], $arguments[1][1], $arguments[1][2]);

        $command = "/usr/bin/ssh -i /Users/max/.ssh/id_rsa ls-project argument='value' --branch '\"'\''fo' 2>&1";
        $this->assertEquals($command, $instance->getCommand());
    }

    public function testGetCommandWithPort()
    {
        $instance = $this->getSSHInstance('/usr/bin/ssh');
        $instance->setPort(29418);

        $commandParts = [
            'ls-project'
        ];
        $arguments = [
            ['argument', 'value', '='],
            ['--branch', '"\'fo', ' ']
        ];

        $instance->addCommandPart($commandParts[0]);

        $instance->addArgument($arguments[0][0], $arguments[0][1], $arguments[0][2]);
        $instance->addArgument($arguments[1][0], $arguments[1][1], $arguments[1][2]);

        $command = "/usr/bin/ssh -p 29418 ls-project argument='value' --branch '\"'\''fo' 2>&1";
        $this->assertEquals($command, $instance->getCommand());
    }

    public function testExecuteCommand()
    {
        $output = 'Gerrie Unit test';

        $instance = $this->getSSHInstance('echo');
        $instance->addArgument('', '', $output);

        $result = $instance->execute(false);

        $this->assertEquals([$output], $result);
    }

    public function testExecuteCommandWithImplode()
    {
        $output = 'Gerrie Unit test - By Gerrie';

        $instance = $this->getSSHInstance('echo');
        $instance->addArgument('', '', $output);

        $result = $instance->execute(true);

        $this->assertEquals($output, $result);
    }
}