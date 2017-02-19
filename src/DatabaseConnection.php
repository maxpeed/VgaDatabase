<?php
/**
 * Created: 2017-02-19 20:53
 */

namespace VgaDatabase;

use \PDO;
use VgaDatabase\Exceptions\VgaConnectionException;


class DatabaseConnection
{
    /** @var DatabaseConfig */
    private $config = null;

    /** @var PDO */
    private $connection = null;

    /** @var \PDOStatement */
    private $PDOStatement;

    public function __construct(string $pathToIniFile)
    {
        $this->config = new DatabaseConfig($pathToIniFile);
    }

    public function write(string $sql, array $values = [], $multi = false): bool
    {
        $result = false;

        if ($this->connect()) {
            $this->PDOStatement = $this->connection->prepare($sql);

            if (!$multi) {
                $values = [$values];
            }

            foreach ($values as $valueSet) {
                $result = $this->PDOStatement->execute($valueSet);
                if ( !$result ) break;
            }


        }
        $this->PDOStatement = null;
        return $result;
    }

    public function read(string $sql)
    {
        $result = [];

        if ($this->connect()) {
            $this->PDOStatement = $this->connection->prepare($sql);
            if ($this->PDOStatement->execute()) {
                $result = $this->PDOStatement->fetchAll();
            }
        }

        $this->PDOStatement = null;
        return $result;
    }

    private function connect(): bool
    {
        if (!$this->isConnected()) {
            try {
                $dsn = $this->config->getDsn();
                $user = $this->config->getUser();
                $password = $this->config->getPassword();
                $options = $this->config->getOptions();
                $this->connection = new PDO($dsn, $user, $password, $options);
            } catch (\PDOException $exception) {
                throw new VgaConnectionException($this->config);
            }
        }

        return $this->isConnected();
    }

    private function isConnected(): bool
    {
        return (
            !empty($this->connection)
            && is_a($this->connection, PDO::class)
        );
    }
}