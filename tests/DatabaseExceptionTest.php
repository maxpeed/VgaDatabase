<?php
/**
 * Created: 2017-06-24 19:36
 */

use Vgait\VgaDatabase\Exceptions\DatabaseConnectionException;
use PHPUnit\Framework\TestCase;

class DatabaseExceptionTest extends TestCase
{
    function testCanThrowException()
    {
            $this->expectException(DatabaseConnectionException::class);

            $message = "";
            $prevException = null;

            throw new DatabaseConnectionException($message,$prevException);

    }
}
