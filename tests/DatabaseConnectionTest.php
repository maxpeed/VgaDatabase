<?php
/**
 * Created: 2017-02-19 20:58
 */


use VgaDatabase\DatabaseConnection;

class DatabaseConnectionTest extends PHPUnit\Framework\TestCase
{

    /** @var string */
    protected $pathToIniFile = __DIR__ . '/database_test.ini';

    /** @var DatabaseConnection */
    protected $databaseConnection;


    public function setUp()
    {
        $this->databaseConnection = new DatabaseConnection($this->pathToIniFile);
    }

    public function testCanConnectToDatabase () {

    }



}
