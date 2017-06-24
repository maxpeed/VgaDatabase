<?php
/**
 * Created: 2017-02-19 20:58
 */


use Vgait\VgaDatabase\DatabaseConnection;
use Vgait\VgaException\VgaException;

/**
 * Class DatabaseConnectionTest
 *
 * This test requires a "tests" database to be setup, and a config file named
 * database_test.ini with correct credentials to be placed in this folder
 *
 */
class DatabaseConnectionTest extends PHPUnit\Framework\TestCase
{

    /** @var string */
    protected $pathToIniFile = __DIR__ . '/database_test.ini';

    /** @var string  */
    protected $pathToFaultySettingsFile = __DIR__ . '/database_test_faulty.ini';

    /** @var string  */
    protected $pathToErrorSettingsFile = __DIR__ . '/database_test_error.ini';

    /** @var DatabaseConnection */
    protected $databaseConnection;


    public function testCanConnect()
    {
        $connection = $this->connect($this->pathToIniFile);
        $this->assertTrue($connection->isConnected());
    }

    public function testCanDisconnect()
    {
        $connection =$this->connect($this->pathToIniFile);
        $connection->disconnect();

        $this->assertFalse($connection->isConnected());
    }

    public function testThrowsConfigurationException()
    {
        $this->expectException(VgaException::class);

        // Should test first error in stack!

        $this->connect($this->pathToErrorSettingsFile);

    }

    public function testThrowsConnectionException()
    {
        $this->expectException(VgaException::class);

        $this->connect($this->pathToFaultySettingsFile);


    }

    public function connect ($settingsFile) {
        $connection =  new DatabaseConnection($settingsFile);
        $connection->connect();
        return $connection;
    }

}
