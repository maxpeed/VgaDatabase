<?php
/**
 * Created: 2017-06-25 12:24
 */

namespace Vgait\VgaDatabase;


use Throwable;
use Vgait\VgaDatabase\Exceptions\DatabaseConnectionException;

class DatabaseStatementException extends DatabaseConnectionException
{

    /** @var string */
    private $sql;

    /**
     * DatabaseStatementException constructor.
     *
     * @param string $message
     * @param string $sql
     * @param Throwable $previousException
     */
    public function __construct(string $message = "",
                                string $sql = "",
                                Throwable $previousException = null)
    {
        $this->sql = $sql;
        parent::__construct($message, $previousException);
    }

    /**
     * @return string
     */
    public function toPrintableString(): string
    {
        return sprintf(
            "DatabaseStatementException: " . PHP_EOL
            . "Parent: %s" . PHP_EOL
            . "SQL: %s " . PHP_EOL,
            parent::toPrintableString(),
            $this->sql
        );
    }
}