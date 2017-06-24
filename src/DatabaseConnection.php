<?php
/**
 * Created: 2017-02-19 20:53
 */

namespace VgaDatabase;

use \PDO;
use PDOException;
use VgaDatabase\Exceptions\VgaDatabaseConfigurationException;
use VgaDatabase\Exceptions\VgaDatabaseException;


/**
 * Class DatabaseConnection
 * @package VgaDatabase
 *
 * Purpose:
 *  Wrap database connection and PDO statement in a single class.
 * Usage:
 *  Inject SQL string, execute it with a set of values. Fetch result if applicable.
 *  Everything else is handled.
 */
class DatabaseConnection
{
    /** @var DatabaseConfig */
    private $configuration = null;

    /** @var PDO */
    private $pdo = null;

    /** @var \PDOStatement */
    private $pdoStatement = null;

    /**
     * DatabaseConnection constructor.
     *
     * @param string $pathToIniFile
     */
    public function __construct(string $pathToIniFile)
    {
        try {

            $this->configuration = new DatabaseConfig($pathToIniFile);

        } catch (VgaDatabaseConfigurationException $e) {
            $message = "Database configuration failed.";
            $sql = null;

            throw new VgaDatabaseConfigurationException($message, null, null, $e);
        }
    }

    /**
     * DatabaseConnection destructor.
     *
     * Destroy all data, and close connection.
     */
    public function __destruct()
    {
        $this->pdoStatement = null;
        $this->pdo = null;
    }

    /**
     * @return bool
     */
    public function connect () {

        return $this->openConnection();

    }

    /**
     * @return bool
     * @throws VgaDatabaseException
     */
    private function openConnection(): bool
    {
        $dsn = $this->configuration->getDsn();
        $user = $this->configuration->getUser();
        $password = $this->configuration->getPassword();
        $options = $this->configuration->getOptions();

        try {

            $this->pdo = new PDO($dsn, $user, $password, $options);
            return true;

        } catch (PDOException $PDOException) {
            $message = "Error when trying to connect to database";
            $sql = ""; // Irrelevant
            $prevVgaException = null; // None

            throw new VgaDatabaseException($message, $sql, $prevVgaException, $PDOException);
        }

    }

}