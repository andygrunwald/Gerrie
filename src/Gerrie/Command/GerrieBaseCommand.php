<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GerrieBaseCommand extends Command
{

    /**
     * Adds the --config-file / -c option to the command.
     *
     * @return void
     */
    protected function addConfigFileOption()
    {
        $this->addOption(
            'config-file',
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to configuration file',
            'Config.yml'
        );
    }

    /**
     * Adds several options for database connections to the command.
     *
     * @return void
     */
    protected function addDatabaseOptions()
    {
        $this
        ->addOption('database-host', 'H', InputOption::VALUE_REQUIRED, 'Name / IP of the host where the database is running')
        ->addOption('database-user', 'u', InputOption::VALUE_REQUIRED, 'Username to access the database')
        ->addOption('database-pass', 'p', InputOption::VALUE_REQUIRED, 'Password to access the database')
        ->addOption('database-port', 'P', InputOption::VALUE_REQUIRED, 'Port where the database is listen')
        ->addOption('database-name', 'N', InputOption::VALUE_REQUIRED, 'Name of the database which should be used');
    }

    /**
     * Adds the SSH Key option to the command.
     *
     * @return void
     */
    protected function addSSHKeyOption()
    {
        $this->addOption(
            'ssh-key',
            'k',
            InputOption::VALUE_REQUIRED,
            'Path to SSH private key for authentication'
        );
    }

    /**
     * Adds a argument of a possible list of instances.
     *
     * Format: scheme://username[:password]@host[:port]/
     * Examples:
     *  * ssh://max.mustermann@review.typo3.org:29418/';
     *  * https://max.mustermann:dummyPassword@review.typo3.org/
     *
     * @return void
     */
    protected function addInstancesArgument()
    {
        $this->addArgument(
            'instances',
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            'List of instances to crawl separated by whitespace. Format scheme://username[:password]@host[:port]/'
        );
    }
}