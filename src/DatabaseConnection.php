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
 */
class DatabaseConnection
{
    /** @var DatabaseConfig */
    private $config = null;

    /** @var PDO */
    private $connection = null;

    /** @var \PDOStatement */
    private $PDOStatement = null;

    /**
     * DatabaseConnection constructor.
     * @param string $pathToIniFile
     */
    public function __construct(string $pathToIniFile)
    {
        $this->config = new DatabaseConfig($pathToIniFile);
    }

    /**
     * Write
     *
     * Use this function when inserting into or altering somthing in database.
     * Any time nothing is expected to be returned, this is a good fit.
     *
     * @param string $sql a valid sql string.
     * @param array $values values for prepared statement.
     * @param bool $multi if the statement is supposed to be used several times.
     * @return bool true on success, false on failure.
     */
    public function write(string $sql, array $values = []): bool
    {
        $result = false;

        if (!$this->openConnection()) {
            return false;
        }

        $this->prepareStatement($sql);

        if (empty($values)) {
            $result = $this->PDOStatement->execute();
        } else { 
            foreach ($values as $valueSet) {
                $result = $this->PDOStatement->execute($valueSet);
                if (!$result) break;
            }
        }

        $this->done();

        return $result;
    }

    public function read(string $sql)
    {
        $result = [];

        if ($this->openConnection()) {
            $this->PDOStatement = $this->connection->prepare($sql);
            if ($this->PDOStatement->execute()) {
                $result = $this->PDOStatement->fetchAll();
            }
        }

        $this->done();

        return $result;
    }

    /**
     * Resets the state of the connection
     *
     * This will close the current statement and release the connection for use in another place. It is very
     * important that you call this function when your current request is done.
     */
    public function done()
    {
        $this->PDOStatement = null;
    }

    /**
     * Creates the database connection
     *
     * In reality this just creates an instance of the PDO class
     *
     * @return bool
     * @throws VgaDatabaseException if connection fails
     */
    private function openConnection(): bool
    {
        if (!$this->isConnected()) {

            $dsn = $this->config->getDsn();
            $user = $this->config->getUser();
            $password = $this->config->getPassword();
            $options = $this->config->getOptions();

            try {

                $this->connection = new PDO($dsn, $user, $password, $options);

            } catch (PDOException $PDOException) {
                $message = "Error when trying to connect to database";
                $sql = ""; // Irrelevant
                $prevVgaException = null; // None

                throw new VgaDatabaseException($message, $sql, $prevVgaException, $PDOException);
            }
        }

        return $this->isConnected();
    }

    /**
     * This will close the connection to the database.
     *
     * Call this function when you know you are not going to use it any more, like at the end of your script.
     */
    public function closeConnection()
    {
        $this->connection = null;
    }

    /**
     * Tests if there is a connection to the database
     *
     * This just checks if there is an instance of a PDO class
     *
     * @return bool
     */
    private function isConnected(): bool
    {
        return (
            !empty($this->connection)
            && is_a($this->connection, PDO::class)
        );
    }

    /**
     * Prepare values and the PDO Statement
     *
     * @param string $sql the SQL string
     * @return bool
     * @throws VgaDatabaseException
     */
    private function prepareStatement(string $sql): bool
    {
        $result = false;

        try {

            $this->PDOStatement = $this->connection->prepare($sql);

        } catch (PDOException $PDOException) {
            $message = "Error when preparing the statement";
            $prevVgaExeption = null;
            throw new VgaDatabaseException($message, $sql, $prevVgaExeption, $PDOException);
        }

        if ($this->PDOStatement) {
            $result = true;
        }

        return $result;
    }

}