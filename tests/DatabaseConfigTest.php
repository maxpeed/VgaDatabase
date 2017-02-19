<?php
/**
 * Created: 2017-02-19 17:54
 */

use VgaDatabase\DatabaseConfig;

class DatabaseConfigTest extends \PHPUnit\Framework\TestCase
{
    protected $pathToIniFile = __DIR__ . '/database_test.ini';

    /** @var DatabaseConfig */
    protected $config;
    protected $parsedIni;

    public function setUp()
    {
        parent::setUp();

        $this->config = new DatabaseConfig($this->pathToIniFile);
        $this->parsedIni = parse_ini_file($this->pathToIniFile);
    }

    public function testDsnStringIsCorrectlyFormatted()
    {
        $expectedDnsString =
            "mysql:" .
            "host=" . $this->parsedIni['host'] . ";" .
            "dbname=" . $this->parsedIni['database'] . ";";

        $realDnsString = $this->config->getDsn();

        $this->assertEquals($expectedDnsString, $realDnsString);
    }

    public function testOptionsArrayIsCorrect()
    {
        $expectedPersist = $this->parsedIni['persist'];
        $expectedErrmode =
            ($this->parsedIni['errormode'] === 'silent') ?
                PDO::ERRMODE_SILENT :
                PDO::ERRMODE_EXCEPTION;

        $expected = [
            PDO::ATTR_PERSISTENT => $this->parsedIni['persist'],
            PDO::ATTR_ERRMODE => $expectedErrmode
        ];

        $result = $this->config->getOptions();

        $this->assertEquals($expected, $result);
    }

    public function testCredentialsIsCorrectlyFormatted()
    {
        $expectedUserName = $this->parsedIni['user'];
        $expectedPassword = $this->parsedIni['password'];

        $resultUser = $this->config->getUser();
        $resultPassword = $this->config->getPassword();

        $this->assertEquals($expectedUserName, $resultUser);
        $this->assertEquals($expectedPassword, $resultPassword);
    }

}
