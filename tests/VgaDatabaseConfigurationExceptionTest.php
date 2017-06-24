<?php
/**
 * Created: 2017-06-24 20:13
 */

use VgaDatabase\Exceptions\DatabaseConfigurationException;
use PHPUnit\Framework\TestCase;

class VgaDatabaseConfigurationExceptionTest extends TestCase
{

    private $message = "Exception Test";
    private $pathToIniFile = "database_test.ini";
    private $faultySettings = [];
    private $errorCode = 0;

    /**
     * @throws DatabaseConfigurationException
     */
    function testDatabaseConfigExceptionIsThrown()
    {
        $this->expectException(DatabaseConfigurationException::class);

        throw new DatabaseConfigurationException();
    }

    function testCanGetPrintableString()
    {
        $this->faultySettings = $this->generateSettingsArray();

        try {
            throw new DatabaseConfigurationException(
                $this->message,
                $this->pathToIniFile,
                $this->faultySettings
            );
        } catch (DatabaseConfigurationException $configurationException) {
            $stringShouldStartWith = "VgaDatabaseConfigurationException:";

            echo $configurationException->toPrintableString();

            $this->assertStringStartsWith(
                $stringShouldStartWith,
                $configurationException->toPrintableString()
            );
        }
    }

    private function generateSettingsArray()
    {
        return parse_ini_file($this->pathToIniFile);
    }


}
