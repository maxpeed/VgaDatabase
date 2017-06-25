<?php
/**
 * Created: 2017-02-19 20:53
 */

namespace Vgait\VgaDatabase;

use \PDO;
use PDOException;
use Vgait\VgaDatabase\Exceptions\DatabaseConfigurationException;
use Vgait\VgaDatabase\Exceptions\DatabaseConnectionException;
use Vgait\VgaException\VgaException;


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

    /**
     * DatabaseConnection constructor.
     *
     * @param string $pathToIniFile
     * @throws VgaException
     */
    public function __construct(string $pathToIniFile)
    {
        try {

            $this->configuration = new DatabaseConfig($pathToIniFile);

        } catch (DatabaseConfigurationException $e) {
            $message = "Database configuration failed.";
            $sql = null;
            $errorCode = 0;

            throw new VgaException($message, $errorCode, $e);
        }
    }

    /**
     * DatabaseConnection destructor.
     *
     * Destroy all data, and close connection.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Redoes connection to the database.
     *
     * Will throw a VgaDatabaseException on error in connection.
     *
     * @return bool
     * @throws DatabaseConnectionException
     */
    public function connect(): bool
    {
        if ($this->isConnected()) {
            return true;
        }

        $dsn = $this->configuration->getDsn();
        $user = $this->configuration->getUser();
        $password = $this->configuration->getPassword();
        $options = $this->configuration->getOptions();

        try {

            if ($this->pdo = new PDO($dsn, $user, $password, $options)) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $PDOException) {

            $message = "Error when trying to connect to database";
            $previousException = $PDOException;

            throw new DatabaseConnectionException($message, $previousException);
        }

    }

    /**
     * Gets a DatabaseQuery Object from the SQL string passed.
     *
     * @param $sql
     * @return DatabaseQuery
     */
    public function getQuery($sql): DatabaseQuery
    {
        $this->connect();
        return new DatabaseQuery($this->pdo->prepare($sql));
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return is_a($this->pdo, PDO::class);
    }

    /**
     * @return bool
     */
    public function disconnect(): bool
    {
        $this->pdoStatement = null;
        $this->pdo = null;

        return true;

    }

}