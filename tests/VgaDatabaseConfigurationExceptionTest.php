<?php
/**
 * Created: 2017-06-24 20:13
 */

use VgaDatabase\Exceptions\VgaDatabaseConfigurationException;
use PHPUnit\Framework\TestCase;

class VgaDatabaseConfigurationExceptionTest extends TestCase
{

    private $message = "Exception Test";
    private $pathToIniFile = "database_test.ini";
    private $faultySettings = [];
    private $errorCode = 0;

    /**
     * @throws VgaDatabaseConfigurationException
     */
    function testDatabaseConfigExceptionIsThrown()
    {
        $this->expectException(VgaDatabaseConfigurationException::class);

        throw new VgaDatabaseConfigurationException();
    }

    function testCanGetPrintableString()
    {
        $this->faultySettings = $this->generateSettingsArray();

        try {
            throw new VgaDatabaseConfigurationException(
                $this->message,
                $this->pathToIniFile,
                $this->faultySettings
            );
        } catch (VgaDatabaseConfigurationException $configurationException) {
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
