<?php
/**
 * Created: 2017-06-24 19:36
 */

use VgaDatabase\Exceptions\VgaDatabaseException;
use PHPUnit\Framework\TestCase;

class VgaDatabaseExceptionTest extends TestCase
{
    function testIsCorrectExceptionThrown()
    {
            $this->expectException(VgaDatabaseException::class);

            $message = "";
            $sql = "";
            $prevException = null;
            $pdoException = null;

            throw new VgaDatabaseException($message, $sql, $prevException, $pdoException);

    }
}
