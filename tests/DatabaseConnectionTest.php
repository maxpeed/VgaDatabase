<?php
/**
 * Created: 2017-02-19 20:58
 */


use VgaDatabase\DatabaseConnection;

class DatabaseConnectionTest extends PHPUnit\Framework\TestCase
{
    protected $pathToIniFile = __DIR__ . '/database_test.ini';

    /** @var DatabaseConnection */
    protected $databaseConnection;


    public function setUp()
    {
        $this->databaseConnection = new DatabaseConnection($this->pathToIniFile);
    }


    public function testCanCreateATable()
    {
        $sql =
            "CREATE TABLE IF NOT EXISTS `test_table` (
                id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                field_int INT(10) NOT NULL,
                field_char VARCHAR(10) NOT NULL,
                field_text TEXT
            )";

        $result = $this->databaseConnection->exe($sql);

        $this->assertTrue($result);
    }

    /**
     * @depends testCanCreateATable
     */
    public function testCanWriteToTable()
    {
        $sql =
            "INSERT INTO `test_table`
              (`field_int`, `field_char`, `field_text`)
            VALUES 
              (:int, :char, :text)";

        $values = [
            [
                'int' => 1234,
                'char' => 'A String',
                'text' => 'Some Text for this field'
            ]
        ];

        $result = $this->databaseConnection->exe($sql, $values);

        foreach ($result as $rowResult) {
            $this->assertTrue($rowResult);
        }
    }

    /**
     * @depends testCanWriteToTable
     */
    public function testCanReadOneRowFromATable()
    {
        $sql =
            "SELECT * FROM `test_table` LIMIT 1";

        $result = $this->databaseConnection->exe($sql);

        $this->assertEquals(1, count($result));
    }

    /**
     * @depends testCanWriteToTable
     */
    public function testCanWriteManyRowsToTable()
    {
        $sql =
            "INSERT INTO `test_table`
              (`field_int`, `field_char`, `field_text`)
            VALUES 
              (:int, :char, :text)";

        $values = [
            [
                'int' => 1234,
                'char' => 'A String',
                'text' => 'Some Text for this field'],
            [
                'int' => 25325235,
                'char' => 'Another String',
                'text' => 'Some Text for this field again'],
            [
                'int' => 28593,
                'char' => 'A String for lks',
                'text' => 'dihghdlfghadfihgdaifhgdfihghdfligh']
        ];

        $result = $this->databaseConnection->exe($sql, $values);

        foreach ($result as $rowResult) {
            $this->assertTrue($rowResult);
        }
    }


    /**
     * @depends testCanWriteToTable
     */
    public function testCanEmptyATable()
    {
        $sql = "TRUNCATE TABLE `test_table`";

        $result = $this->databaseConnection->exe($sql);

        $this->assertTrue($result);
    }

    /**
     * @depends testCanCreateATable
     */
    public function testCanDropATable()
    {
        $sql = "DROP TABLE `test_table`";

        $result = $this->databaseConnection->exe($sql);

        $this->assertTrue($result, "Table dropped");

    }


}
