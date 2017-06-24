<?php
/**
 * Created: 2017-06-24 19:36
 */

use VgaDatabase\Exceptions\DatabaseConnectionException;
use PHPUnit\Framework\TestCase;

class VgaDatabaseExceptionTest extends TestCase
{
    function testIsCorrectExceptionThrown()
    {
            $this->expectException(DatabaseConnectionException::class);

            $message = "";
            $sql = "";
            $prevException = null;
            $pdoException = null;

            throw new DatabaseConnectionException($message, $sql, $prevException, $pdoException);

    }
}
