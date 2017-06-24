<?php
/**
 * Created: 2017-06-24 23:32
 */

use Vgait\VgaDatabase\DatabaseConnection;
use Vgait\VgaDatabase\DatabaseQuery;
use PHPUnit\Framework\TestCase;

class DatabaseQueryTest extends TestCase
{
    /** @var string */
    protected $settingsFile = __DIR__ . '/database_test.ini';
    private $connection;

    /**
     * Set up current test
     *
     * Create database connection
     */
    public function setUp()
    {
        parent::setUp();
        $this->connection = new DatabaseConnection($this->settingsFile);
    }

    public function testCreateTable()
    {
        $sql =
            "CREATE TABLE `test` (
                  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `street` VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `zip` INT(5) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        $dbquery = new DatabaseQuery($this->connection, $sql);

        $this->assertTrue($dbquery->state());
    }

    public function testInsertData()
    {
        $sql = "INSERT INTO `test` (`id`, `name`, `street`, `zip`)
                VALUES
                    (1,'VgaIT','Sup',23223),
                    (2,'Mupp','Kalypso',12345),
                    (3,'Mapp','Redo',43255),
                    (4,'Lito Bah','Knas',82838),
                    (5,'Juker','Sabbath',48848),
                    (6,'Hallden','Hulda',47373),
                    (7,'Hildur','Hansa',37719),
                    (8,'Kneip','Knapp',17210),
                    (9,'Blupp','naYhh',94299),
                    (10,'Knister','Lansn',82728)";


    }

    public function testReadFromDB()
    {
        $sql = "SELECT * FROM `test`";

    }

    public function testRemoveRowFromPreparedValues()
    {
        $sql = "DELETE FROM `test` WHERE `id` = :id";

        $values = ['id' => '8'];



    }

    public function testAddRowsFromPreparedValues()
    {
        $sql = "INSERT INTO `test` (`name`, `street`, `zip`)
                VALUES (:name,:street,:zip)";

        $values = [
            ['name' => 'Vera Kruz', 'street' => 'St Lane', 'zip' => '12345'],
            ['name' => 'Michelle Pfeiffer', 'street' => 'Kryz Rd', 'zip' => '12345']
        ];
    }

    public function testTrunchateTable()
    {
        $sql = "TRUNCATE TABLE `test`";
    }

    public function testRemoveTable()
    {
        $sql = "DROP TABLE `test`";
    }

    private function createDbQuery($sql)
    {
        $dbquery = new DatabaseQuery($this->connection, $sql);

        return $dbquery->state();
    }

    private function executeQuery ($sql, $values) {

    }


}
