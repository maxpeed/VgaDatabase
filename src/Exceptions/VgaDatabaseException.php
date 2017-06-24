<?php
/**
 * Created: 2017-01-01 23:13
 */

namespace VgaDatabase\Exceptions;

use PDOException;
use VgaException\VgaException;
use VgaException\VgaExceptionType;

class VgaDatabaseException extends VgaException
{

    /** @var string */
    private $sql = "";

    /** @var PDOException */
    private $originalPdoExeption = null;

    /**
     * VgaDatabaseException constructor.
     *
     * @param string $message
     * @param string $sql
     * @param VgaExceptionType|null $previousException
     * @param PDOException|null $PDOException
     */
    public function __construct(string $message = "",
                                string $sql = "",
                                VgaExceptionType $previousException = null,
                                PDOException $PDOException = null)
    {
        $this->sql = $sql;
        $this->originalPdoExeption = $PDOException;
        $errorCode = 0;

        parent::__construct($message, $errorCode, $previousException);
    }

    /**
     * Return a printable string that represents this error.
     *
     * @return string
     */
    public function toPrintableString(): string
    {
        return sprintf(
            "VgaDatabaseException thrown. " . PHP_EOL
            . "%s" . PHP_EOL
            . "SQL: %s" . PHP_EOL
            . "PDO Message: %s" . PHP_EOL,

            parent::toPrintableString(),
            $this->sql,
            empty($this->originalPdoExeption->getMessage()) ? null : $this->originalPdoExeption->getMessage()
        );
    }
}
