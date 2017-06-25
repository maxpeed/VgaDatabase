<?php
/**
 * Created: 2017-06-24 23:31
 */

namespace Vgait\VgaDatabase;


use PDOException;
use PDOStatement;
use Vgait\VgaDatabase\Exceptions\DatabaseConnectionException;

class DatabaseQuery
{

    const STATE_ERROR = 0;
    const STATE_READY = 1;
    const STATE_EXECUTED  = 2;

    /**
     * @var PDOStatement
     */
    private $PDOStatement;

    private $state = 0;

    /**
     * DatabaseQuery constructor.
     * @param PDOStatement $PDOStatement
     */
    public function __construct(PDOStatement $PDOStatement)
    {
        $this->PDOStatement = $PDOStatement;
    }

    /**
     * @return int
     */
    public function state () {
        return $this->state;
    }

    /**
     * Executes the query.
     *
     * If this is a prepared statement, values as array with keys corresponding to the
     * names in SQL String is required. Otherwise an exception will be thrown.
     *
     * @param array|null $values
     */
    public function execute (array $values = null) {

        try {

            $this->PDOStatement->execute($values);

        } catch (PDOException $PDOException) {

            $message = "Bad statement execution";
            $sql = $this->PDOStatement->queryString;

            throw new DatabaseStatementException($message, $sql, $PDOException);
        }
    }


}