<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Service;

use Gerrie\Component\Database\Database;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseService
{

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $logger = null;

    /**
     * @var \Gerrie\Component\Database\Database
     */
    protected $database = null;

    /**
     * Constructor
     *
     * @param Database $database
     * @param OutputInterface $output
     */
    public function __construct(Database $database, OutputInterface $output)
    {
        $this->database = $database;
        $this->logger = $output;
    }

    /**
     * Creates the necessary database tables, if they are not exists.
     *
     * @return bool
     * @throws \Exception
     */
    public function setupDatabaseTables()
    {
        $logger = $this->getLogger();
        $database = $this->getDatabase();

        $databaseHandle = $database->getDatabaseConnection();
        $tableDefinition = $database->getTableDefinition();

        $tables = array_keys($tableDefinition);
        foreach ($tables as $tableName) {
            $logger->writeln('<info>Table "' . $tableName . '"</info>');

            $statement = $databaseHandle->prepare('SHOW TABLES LIKE :table');
            $statement->bindParam(':table', $tableName, \PDO::PARAM_STR);
            $statement->execute();

            if ($statement->rowCount() == 1) {
                $logger->writeln('<info>=> Exists. Skip it</info>');
                continue;
            }

            // Table does not exists. Try to create it
            $createTableResult = $databaseHandle->query($tableDefinition[$tableName]);

            if ($createTableResult === false) {
                $databaseError = $databaseHandle->errorInfo();
                $message = 'Table "%s" could not be created. %s (%s)';
                $message = sprintf($message, $tableName, $databaseError[2], $databaseError[1]);
                throw new \Exception($message, 1398100879);

            } else {
                $logger->writeln('<info>Not exists. Created</info>');
            }
        }

        return true;
    }

    /**
     * Returns the database object
     *
     * @return Database
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Returns the logger object
     *
     * @return OutputInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}