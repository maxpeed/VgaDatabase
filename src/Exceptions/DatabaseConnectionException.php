<?php
/**
 * Created: 2017-01-01 23:13
 */

namespace Vgait\VgaDatabase\Exceptions;


use PDOException;
use Throwable;
use Vgait\VgaException\VgaException;
use Vgait\VgaException\VgaExceptionType;

class DatabaseConnectionException extends VgaException
{

    /** @var PDOException */
    private $originalPdoExeption = null;

    /**
     * VgaDatabaseException constructor.
     *
     * @param string $message
     * @param Throwable|null $previousException
     */
    public function __construct(string $message = "",
                                Throwable $previousException = null)
    {

        if (is_a($previousException, PDOException::class)) {
            $this->originalPdoExeption = $previousException;
            $previousException = null; //make sure wrong type is not passed in chain
        } else if (is_a($previousException, VgaExceptionType::class)) {
            // Do nothing, this just have to be here to make sure that VgaExceptions
            // are chained
        } else {
            $previousException = null;
        }

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
        $parentMessage = parent::toPrintableString();
        $pdoMessageString = empty($this->originalPdoExeption) ?
            null : $this->originalPdoExeption->getMessage();

        return sprintf(
            "VgaDatabaseException thrown. " . PHP_EOL
            . "%s" . PHP_EOL
            . "PDO Message: %s" . PHP_EOL,

            $parentMessage,
            $pdoMessageString
        );
    }
}
