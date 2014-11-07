<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Check;

/**
 * CheckInterface is an interface to unify checks for the gerrie:check command.
 *
 * @author Andreas Grunwald <andygrunwald@gmail.com>
 */
interface CheckInterface
{
    /**
     * Executes the check itselfs
     *
     * @return boolean
     */
    public function check();

    /**
     * Returns the message, if the check succeeded
     *
     * @return string
     */
    public function getSuccessMessage();

    /**
     * Returns the message, if the check fails
     *
     * @return string
     */
    public function getFailureMessage();
}
