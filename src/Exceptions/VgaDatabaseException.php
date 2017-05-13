<?php
/**
 * Created: 2017-01-01 23:13
 */

namespace VgaDatabase\Exceptions;

use PDOException;

class VgaDatabaseException extends VgaException
{

    /** @var string */
    private $sql = "";

    /** @var PDOException */
    private $originalPdoExeption = null;

    /**
     * VgaDatabaseException constructor.
     */
    public function __construct($message = "", $sql = "", VgaException $previousException = null, PDOException $PDOException = null)
    {
        $this->sql = $sql;
        $this->originalPdoExeption = $PDOException;

        parent::__construct($message, $previousException);
    }

    /**
     * Return a printable string that represents this error.
     *
     * @return string
     */
    public function toPrintableString(): string
    {
        $msg = !empty($this->msg) ?
            "<p>Message: {$this->getMessage()}</p>" : "";

        $sql = !empty($this->sql) ?
            " <pre>{$this->sql}</pre>" : "";

        $PDOErrorMessage = !empty($this->previousVgaException) ?
            $this->originalPdoExeption->getMessage() : "";

        return "$msg $sql $PDOErrorMessage";
    }
}
