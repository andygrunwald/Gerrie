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

use Gerrie\Component\Database\Database;

/**
 * Checks if the database connection works.
 *
 * @author Andreas Grunwald <andygrunwald@gmail.com>
 */
class DatabaseConnectionCheck implements CheckInterface
{

    /**
     * Database object
     *
     * @var Database
     */
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Executes the check itselfs
     *
     * @return boolean
     */
    public function check()
    {
        $result = true;

        // TODO This check tries only to connect to the database
        // But it would make sense to check
        //  a) insert, select, update, delete, etc. access
        //  a) a) Which access rights do i need? Topic hardening?
        try {
            $this->database->connect($this->database->getConfig());
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * Returns the message, if the check succeeded
     *
     * @return string
     */
    public function getSuccessMessage()
    {
        $databaseConfig = $this->database->getConfig();
        $message = 'Database connection to host "%s" works as expected.';
        $message = sprintf($message, $databaseConfig['Host']);

        return $message;
    }

    /**
     * Returns the message, if the check fails
     *
     * @return string
     */
    public function getFailureMessage()
    {
        $databaseConfig = $this->database->getConfig();
        $message = 'Database connection to host "%s" works not as expected. Please check your credentials or setup';
        $message = sprintf($message, $databaseConfig['Host']);

        return $message;
    }

    /**
     * Returns if this check is optional or required.
     *
     * @return bool
     */
    public function isOptional()
    {
        return false;
    }
}
