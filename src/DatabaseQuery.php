<?php
/**
 * Created: 2017-06-24 23:31
 */

namespace Vgait\VgaDatabase;

use PDO;
use PDOException;
use PDOStatement;
use Vgait\VgaDatabase\Exceptions\DatabaseStatementException;

class DatabaseQuery
{

    const STATE_ERROR = 0;
    const STATE_READY = 1;
    const STATE_EXECUTED = 2;

    /**
     * @var PDOStatement
     */
    private $PDOStatement;

    private $state = 0;

    /**
     * DatabaseQuery constructor.
     *
     * @param PDOStatement $PDOStatement
     */
    public function __construct(PDOStatement $PDOStatement)
    {
        $this->PDOStatement = $PDOStatement;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * Executes the query.
     *
     * If this is a prepared statement, values as array with keys corresponding to the names in
     * SQL String is required.
     *
     * Function calls can be chained.
     *
     * If this function fails an DatabaseStatementException will be thrown.
     *
     * @param array|null $values
     *
     * @return DatabaseQuery
     * @throws DatabaseStatementException
     */
    public function execute(array $values = NULL): DatabaseQuery
    {
        try {
            if ( $this->PDOStatement->execute($values) ) {

                $this->setState(self::STATE_EXECUTED);
                return $this;

            }
            else {
                $message = "Bad statement execution";
                $sql = $this->PDOStatement->queryString;
                throw new DatabaseStatementException($message, $sql);
            }
        }
        catch ( PDOException $PDOException ) {
            $message = "Statement threw PDO Exception.";
            $sql = $this->PDOStatement->queryString;
            throw new DatabaseStatementException($message, $sql, $PDOException);
        }
    }

    private function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return array
     * @throws DatabaseStatementException
     */
    public function fetchAllToArray(): array
    {
        $rows = [];
        if ( $this->getState() != self::STATE_EXECUTED ) {
            $message = "Statement has not been executed";
            $sql = $this->PDOStatement->queryString;
            throw new DatabaseStatementException($message, $sql);
        }

        $fetchStyle = PDO::FETCH_ASSOC;
        $rows = $this->PDOStatement->fetchAll($fetchStyle);

        return $rows;
    }

}