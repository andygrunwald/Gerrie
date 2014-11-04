<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Component\Console;

use Symfony\Component\Console\Input\ArgvInput;

/**
 * ArgvInputExtended is a class to implement the extended version of InputInterface "InputExtendedInterface"
 * to complete missing functionality.
 *
 * @author Andreas Grunwald <andygrunwald@gmail.com>
 *
 * @api
 */
class ArgvInputExtended extends ArgvInput implements InputExtendedInterface
{

    /**
     * Returns true if a option is set during the command call.
     *
     * This method is useful if you want to know if an option was passed during the command call.
     * Sometimes this is useful, e.g. to overwrite only values if you pass them.
     *
     * Example definition in a command:
     *      protected function configure() {
     *          $this->setName('my:command')
     *               ->addOption('foo', 'f', InputOption::VALUE_REQUIRED, 'Description', 'Default Value');
     *          ...
     *
     * Example:
     *      $ ./console my:command --foo=bar
     *      $input->isOptionSet('foo') => true
     *
     *      $ ./console my:command
     *      $input->isOptionSet('foo') => false
     *
     * @param string $optionName Name of the option you want to check ('foo' from example).
     * @return boolean
     */
    public function isOptionSet($optionName)
    {
        return array_key_exists($optionName, $this->options);
    }
}
