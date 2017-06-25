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

    /** @var DatabaseConnection */
    private $dbInstance;

    /**
     * Set up current test
     *
     * Create database connection
     */
    public function setUp()
    {
        parent::setUp();
        $this->dbInstance = new DatabaseConnection($this->settingsFile);
        $this->dbInstance->connect();
    }

    public function testCreateTable()
    {
        $sql =
          "CREATE TABLE `test` (
                  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `street` VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `zip` INT(5) NOT NULL,
            PRIMARY KEY (`id`) )
            ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        $state = $this->executeQuery($sql);

        $expectedState = DatabaseQuery::STATE_EXECUTED;
        $this->assertEquals($expectedState, $state);
    }

    public function testInsertData()
    {
        $sql =
          "INSERT INTO `test` (`id`, `name`, `street`, `zip`)
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

        $state = $this->executeQuery($sql);

        $expectedState = DatabaseQuery::STATE_EXECUTED;
        $this->assertEquals($expectedState, $state);
    }

    public function testReadFromDB()
    {
        $sql = "SELECT * FROM `test`";
        $result = FALSE;

        $fetchedRows = $this->executeReadQuery($sql)->fetchAllToArray();

        foreach ( $fetchedRows as $row ) {
            if ( is_array($row)
                 && array_key_exists('id', $row)
            ) {
                $result = TRUE;
            }
            else {
                $result = FALSE;
            }
            echo "Read";
            $this->assertTrue($result);
        }

        $this->assertTrue($result);
    }

    public function testRemoveRowFromPreparedValues()
    {
        $sql = "DELETE FROM `test` WHERE `id` = :id";

        $values = ['id' => '8'];

        $this->assertTrue(TRUE);
    }

    public function testAddRowsFromPreparedValues()
    {
        $sql = "INSERT INTO `test` (`name`, `street`, `zip`)
                VALUES (:name,:street,:zip)";

        $values = [
          ['name' => 'Vera Kruz', 'street' => 'St Lane', 'zip' => '12345'],
          ['name' => 'Michelle Pfeiffer', 'street' => 'Kryz Rd', 'zip' => '12345'],
        ];


        $this->assertTrue(TRUE);
    }

    /**
     * @depends testCreateTable
     */
    public function testTruncateTable()
    {
        $sql = "TRUNCATE TABLE `test`";
        $state = $this->executeQuery($sql);

        $expectedState = DatabaseQuery::STATE_EXECUTED;
        $this->assertEquals($expectedState, $state);
    }

    /**
     * @depends testTruncateTable
     */
    public function testRemoveTable()
    {
        $sql = "DROP TABLE `test`";
        $state = $this->executeQuery($sql);

        $expectedState = DatabaseQuery::STATE_EXECUTED;
        $this->assertEquals($expectedState, $state);
    }

    private function executeQuery($sql, $values = []): int
    {
        $dbquery = $this->dbInstance->getQuery($sql);
        return $dbquery->execute($values)->getState();
    }

    private function executeReadQuery($sql, $values = []): DatabaseQuery
    {
        return $this->dbInstance->getQuery($sql)->execute($values);
    }

}
