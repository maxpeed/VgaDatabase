<?php
/**
 * Created: 2017-06-24 23:31
 */

namespace Vgait\VgaDatabase;

use PDO;
use PDOException;
use PDOStatement;
use Vgait\VgaDatabase\Exceptions\DatabaseStatementException;
use Vgait\VgaException\VgaException;
use Vgait\VgaException\VgaExceptionType;

class DatabaseQuery
{

    /************************************************************
     * Constants
     * /************************************************************/
    const STATE_DEFAULT = 0;
    const STATE_ERROR = 0;
    const STATE_READY = 1;
    const STATE_EXECUTED = 2;
    const STATE_SUCCESSFUL = 10;

    const OUTPUT_FETCH_ASSOC = PDO::FETCH_ASSOC;
    const OUTPUT_FETCH_DEFAULT = self::OUTPUT_FETCH_ASSOC;

    const OPERATION_INPUT = 256;
    const OPERATION_OUTPUT = 257;

    const OPERATION_DEFAULT = self::OPERATION_INPUT;

    /************************************************************
     * MEMBERS
     *************************************************************/

    /** @var PDOStatement */
    private $PDOStatement;

    /************************************************************
     * FLAGS
     *************************************************************/

    private $state = self::STATE_DEFAULT;
    private $operation = self::OPERATION_DEFAULT;
    private $multi = FALSE;
    private $outputFetchMode = self::OUTPUT_FETCH_DEFAULT;
    private $values = [];

    /**************************************************************
     * DatabaseQuery constructor.
     *
     * @param PDOStatement $PDOStatement
     *************************************************************/
    public function __construct(PDOStatement $PDOStatement)
    {
        $this->PDOStatement = $PDOStatement;
        $this->setState(self::STATE_READY);
    }

    /************************************************************
     * PUBLIC METHODS
     */

    /**
     * @return array|bool|null|object
     * @throws VgaException
     */
    public function execute()
    {
        $result = NULL;

        try {
            switch ( $this->operation ) {
                case self::OPERATION_OUTPUT :
                    $result = $this->queryWithOutput($this->values);
                    break;
                case self::OPERATION_INPUT:
                    $result = $this->queryWithoutOutput($this->values);
                    break;
                default:

            }

            return $result;
        }
        catch ( VgaExceptionType $vgaException ) {
            $message = "Caught VgaException in " . __FUNCTION__;
            $errorCode = $vgaException->getCode();
            throw new VgaException($message, $errorCode, $vgaException);
        }
    }

    public function setValuesSingle(array $values): DatabaseQuery
    {
        $this->multi = FALSE;
        $this->values = $values;

        return $this;
    }

    public function setValuesMulti(array $values): DatabaseQuery
    {
        $this->multi = TRUE;
        $this->values = $values;

        return $this;

    }

    public function fetchAsArray(): DatabaseQuery
    {
        $this->setOperation(self::OPERATION_OUTPUT);
        $this->outputFetchMode = self::OUTPUT_FETCH_ASSOC;
        return $this;
    }

    public function inputQuery(): DatabaseQuery
    {
        $this->setOperation(self::OPERATION_INPUT);
        return $this;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /************************************************************
     * INTERNAL METHODS
     */

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
     * @return bool
     * @throws DatabaseStatementException
     */
    private function executeQuery(array $values = []): bool
    {
        try {
            if ( $this->getState() >= self::STATE_READY ) {
                $this->setState(self::STATE_READY);

                if ( $this->PDOStatement->execute($values) ) {

                    $this->setState(self::STATE_EXECUTED);
                    return TRUE;
                }
            }

            $message = "Bad statement execution";
            $sql = $this->PDOStatement->queryString;
            throw new DatabaseStatementException($message, $sql);

        }
        catch ( PDOException $PDOException ) {
            $message = "Statement threw PDO Exception.";
            $sql = $this->PDOStatement->queryString;
            throw new DatabaseStatementException($message, $sql, $PDOException);
        }
    }

    /**
     * Runs when Input Query is selected
     *
     * @param array $values
     *
     * @return bool
     * @throws DatabaseStatementException
     */
    private function queryWithoutOutput(array $values = []): bool
    {
        // todo: Validate members
        $result = FALSE;

        if ( $this->multi === FALSE ) {
            // single mode
            $result = $this->executeQuery($values);
        }
        else {
            // multi mode
            foreach ( $values as $valueSet ) {
                // breaks on error
                $result = $this->executeQuery($valueSet);
                if ( !$result ) {
                    break;
                }
            }
        }

        if ( !$result ) {
            $this->setState(self::STATE_ERROR);
            $message = "Exception thrown in " . __FUNCTION__;
            $sql = $this->PDOStatement->queryString;
            throw new DatabaseStatementException($message, $sql);
        }

        $this->setState(self::STATE_SUCCESSFUL);
        return $result;
    }

    /**
     * @param array $values
     *
     * @return array|null
     * @throws DatabaseStatementException
     */
    private function queryWithOutput(array $values)
    {
        // todo: validate members

        $result = NULL;
        $fetchStyle = $this->outputFetchMode;

        if ( !$this->multi ) {
            //single
            if ( $this->executeQuery($values) ) {
                $result = $this->PDOStatement->fetchAll($fetchStyle);
            }
        }
        else {
            //multi
            foreach ( $values as $valueSet ) {
                $this->executeQuery($valueSet);
                $result[] = $this->PDOStatement->fetchAll($fetchStyle);
            }
        }

        return $result;
    }

    /**
     * @param $state
     */
    private function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @param int $operation
     */
    private function setOperation(int $operation)
    {
        $this->operation = $operation;
    }

}