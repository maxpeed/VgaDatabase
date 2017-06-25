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
        echo PHP_EOL;
        $sql =
          "CREATE TABLE `test` (
                  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `street` VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                  `zip` INT(5) NOT NULL,
            PRIMARY KEY (`id`) )
            ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        // $state = $this->dbInstance->getQuery($sql)->execute()->getState();
        $state = $this->dbInstance->getQuery($sql)
                                  ->execute();

        $expectedState = TRUE;
        $this->assertEquals($expectedState, $state);
        echo __FUNCTION__ . ": Reported state = $state - expected = $expectedState " . PHP_EOL;
    }

    /**
     * @depends testCreateTable
     */
    public function testInsertData()
    {
        $expectedState = TRUE;

        echo PHP_EOL;
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

        $state = $this->dbInstance->getQuery($sql)
                                  ->execute();

        $this->assertEquals($expectedState, $state);

        echo __FUNCTION__ . ": Reported state = $state - expected = $expectedState " . PHP_EOL;
    }

    /**
     * @depends testInsertData
     */
    public function testReadFromDB()
    {
        echo PHP_EOL;
        $sql = "SELECT * FROM `test`";
        $result = FALSE;
        $totalRows = 0;
        $okRows = 0;

        $fetchedRows =
          $this->dbInstance->getQuery($sql)
                           ->fetchAsArray()
                           ->execute();

        foreach ( $fetchedRows as $row ) {
            if ( is_array($row)
                 && array_key_exists('id', $row)
            ) {
                $result = TRUE;
                $okRows++;
            }
            else {
                $result = FALSE;
            }

            $totalRows++;
            $this->assertTrue($result);
        }

        echo __FUNCTION__ . ": $okRows of $totalRows is OK " . PHP_EOL;
    }

    /**
     * @depends testInsertData
     */
    public function testReadFromDBWithValues()
    {
        echo PHP_EOL;
        $sql = "SELECT * FROM `test` WHERE `id` = :id";
        $values = ['id' => 1];
        $result = FALSE;
        $totalRows = 0;
        $okRows = 0;

        $fetchedRows =
          $this->dbInstance->getQuery($sql)
                           ->setValuesSingle($values)
                           ->fetchAsArray()
                           ->execute();

        foreach ( $fetchedRows as $row ) {
            if ( is_array($row)
                 && array_key_exists('id', $row)
            ) {
                $result = TRUE;
                $okRows++;
            }
            else {
                $result = FALSE;
            }
            $totalRows++;
            $this->assertTrue($result);
        }
        echo __FUNCTION__ . ": $okRows of $totalRows is OK " . PHP_EOL;
    }

    /**
     * @depends testInsertData
     */
    public function testReadFromDbWithMultipleValueSets()
    {
        echo PHP_EOL;
        $sql = "SELECT * FROM `test` WHERE `id` = :id";

        $values = [['id' => 2], ['id' => '6']];
        $totalRows = 0;
        $okRows = 0;

        $result =
          $this->dbInstance->getQuery($sql)
                           ->fetchAsArray()
                           ->setValuesMulti($values)
                           ->execute();

        foreach ( $result as $resultSet ) {
            foreach ( $resultSet as $row ) {
                $status = FALSE;
                $totalRows++;
                if ( array_key_exists('id', $row) ) {
                    $status = TRUE;
                    $okRows++;
                }

                $this->assertTrue($status);
            }
        }
        echo __FUNCTION__ . ": $okRows of $totalRows is OK " . PHP_EOL;
    }

    /**
     * @depends testInsertData
     */
    public function testRemoveRowFromPreparedValues()
    {
        echo PHP_EOL;
        $sql = "DELETE FROM `test` WHERE `id` = :id";
        $values = ['id' => '8'];
        $exectedStatus = TRUE;

        $status =
          $this->dbInstance->getQuery($sql)
                           ->inputQuery()
                           ->setValuesSingle($values)
                           ->execute();

        $this->assertEquals($status, $exectedStatus);
        echo __FUNCTION__ . ": reported status = $status - expected: $exectedStatus " . PHP_EOL;
    }

    /**
     * @depends testCreateTable
     */
    public function testAddRowsFromSeveralPreparedValues()
    {
        echo PHP_EOL;
        $sql = "INSERT INTO `test` (`name`, `street`, `zip`)
                VALUES (:name,:street,:zip)";
        $totalRows = 0;
        $okRows = 0;
        $expectedStatus = TRUE;

        $values = [
          ['name' => 'Vera Kruz', 'street' => 'St Lane', 'zip' => '12345'],
          ['name' => 'Michelle Pfeiffer', 'street' => 'Kryz Rd', 'zip' => '12345'],
        ];
        try {
            $status = $this->dbInstance->getQuery($sql)
                                       ->setValuesMulti($values)
                                       ->inputQuery()
                                       ->execute();
            $this->assertEquals($expectedStatus, $status);
        }
        catch ( \Vgait\VgaException\VgaExceptionType $exception ) {
            echo $exception->fullStackToPrintableString();
        }

        echo __FUNCTION__ . ": reported status = $status - expected: $expectedStatus " . PHP_EOL;
    }

    /**
     * @depends testCreateTable
     */
    public function testTruncateTable()
    {
        echo PHP_EOL;

        $sql = "TRUNCATE TABLE `test`";
        $state =
          $this->dbInstance->getQuery($sql)
                           ->execute();

        $expectedState = TRUE;
        $this->assertEquals($expectedState, $state);

        echo __FUNCTION__ . ": Expect state = $expectedState - actual = $state." . PHP_EOL;
    }

    /**
     * @depends testTruncateTable
     */
    public function testRemoveTable()
    {
        $sql = "DROP TABLE `test`";
        $state =
          $this->dbInstance->getQuery($sql)
                           ->execute();

        $expectedState = TRUE;
        $this->assertEquals($expectedState, $state);
        echo __FUNCTION__ . ": Expect state = $expectedState - actual = $state." . PHP_EOL;
    }

}
