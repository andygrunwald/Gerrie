<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Component\Connection;

class SSH
{

    protected $keyFile = '';

    protected $port = 0;

    protected $commandParts = array();

    protected $arguments = array();

    protected $executable = '';

    public function __construct($executable, array $config)
    {
        $this->setExecutable($executable);
        $this->setKeyFile($config['KeyFile']);
        $this->setPort($config['Port']);
    }

    /**
     * Sets the SSH port
     *
     * @param int $port Port number
     * @return void
     */
    public function setPort($port)
    {
        $this->port = intval($port);
    }

    /**
     * Gets the SSH port for commands
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the private key file
     * e.g. ~/.ssh/id_rsa
     *
     * @param string $keyFile Path to private key file
     * @return void
     */
    public function setKeyFile($keyFile)
    {
        /*
         * TODO Implement a kind of existence check of SSH key
        if (file_exists($keyFile) === false) {
            $exceptionMessage = 'SSH keyfile "%s" does not exists.';
            $exceptionMessage = sprintf($exceptionMessage, $keyFile);
            throw new \RuntimeException($exceptionMessage, 1415536217);
        }
        */

        $this->keyFile = $keyFile;
    }

    /**
     * Gets the path of the private key file
     *
     * @return string
     */
    public function getKeyFile()
    {
        return $this->keyFile;
    }

    /**
     * Prepares and builds the full command.
     * All properties, like ssh key, port, command and agruments will be considered
     *
     * @return string
     */
    protected function prepareCommand()
    {
        $command = $this->getExecutable() . ' ';

        $keyFile = $this->getKeyFile();
        // @todo add file_exists($keyFile) === true
        // At the moment this is not working, because i don`t know a PHP function to resolve ~/.ssh/private_key_file
        // https://twitter.com/andygrunwald/status/315413070904184832
        // Further more, $keyFile can`t be escaped with escapeshellarg(),
        // because after this the command is not working anymore
        if ($keyFile) {
            $command .= '-i ' . $keyFile . ' ';
        }

        $port = $this->getPort();
        if ($port > 0) {
            $command .= '-p ' . $port . ' ';
        }

        $command .= implode(' ', $this->getCommandParts()) . ' ';
        $command .= implode(' ', $this->getArguments());
        $command .= ' 2>&1';

        return $command;
    }

    /**
     * Executes the built command.
     * Returns the output of the command.
     *
     * @param bool $implodeReturnValue True if the output of the executed command will be imploded. False otherwise
     * @return array|string
     */
    public function execute($implodeReturnValue = true)
    {
        $data = array();
        $command = $this->getCommand();

        $data = $this->execCommand($command, $data);

        if ($implodeReturnValue === false) {
            return $data;
        }

        return implode('', $data);
    }

    /**
     * Wrapped exec()-call.
     * This makes unit testing possible.
     *
     * @param string $command The command to execute
     * @param array $data Array where the result will be stored
     * @return array
     */
    private function execCommand($command, array $data)
    {
        exec($command, $data);

        return $data;
    }

    /**
     * Returns the full command, which will be executed.
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->prepareCommand();
    }

    /**
     * Sets the path of the SSH executable
     *
     * @param string $executable Path to the executable
     * @return void
     * @throws \Exception
     */
    public function setExecutable($executable)
    {
        /*
         * TODO This executable check works not as expected :( Fix this
         * is_executable('ssh') => false
         * is_executable('/usr/bin/ssh') => true
         *
        if (is_executable($executable) === false) {
            throw new \Exception('SSH executable is not executable!', 1364032483);
        }
        */

        $this->executable = $executable;
    }

    /**
     * Gets the path of the SSH executable
     *
     * @return string
     */
    public function getExecutable()
    {
        return $this->executable;
    }

    /**
     * Gets all command parts
     *
     * @see $this->addCommandPart()
     *
     * @return array
     */
    public function getCommandParts()
    {
        return $this->commandParts;
    }

    /**
     * Adds a command part to the command.
     *
     * e.g.
     *        Command: ./console gerrie:export
     *                 => "gerrie:export" is a command part
     *
     *        Command: git merge
     *                 => "merge" is a command part
     *
     * @param string $commandPart The command part
     * @return void
     */
    public function addCommandPart($commandPart)
    {
        $this->commandParts[] = ($commandPart);
    }

    /**
     * Gets all command arguments
     *
     * @see $this->addArgument()
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Adds a new argument to the SSH command.
     *
     * e.g.
     *        --help        => $agrument = '--help', $value = '', $glue = ''
     *        --foo=bar    => $agrument = '--foo', $value = 'bar', $glue = '='
     *        --foo bar    => $agrument = '--foo', $value = 'bar', $glue = ''
     *
     * @param string $argument Name of argument
     * @param string $value Value of argument
     * @param string $glue Concat value of $argument and $value
     * @return void
     */
    public function addArgument($argument, $value, $glue)
    {
        $escapedValue = (($value) ? escapeshellarg($value) : '');
        $this->arguments[] = $argument . $glue . $escapedValue;
    }

    /**
     * Resets all command specific parts.
     * This can be used to fire many ssh commands with one ssh object.
     * Just reset() all the seetings before a new command is setted up.
     *
     * @return void
     */
    public function reset()
    {
        $this->commandParts = array();
        $this->arguments = array();
    }
}