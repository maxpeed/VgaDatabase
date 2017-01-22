<?php

/**
 * Created by PhpStorm.
 * User: hh
 * Date: 2016-07-19
 * Time: 21:12
 */

namespace Libs\VgaDatabase;

use Libs\VgaDatabase\VgaDatabaseException;
use \PDO;
use \PDOException;
use \PDOStatement;


class VgaDBC extends PDO {

	/**
	 * Database connection setup
	 */
	private $database;
	private $user;
	private $password;
	private $charset;
	private $host;
	private $opts = [];

	/** @var bool Flag for connection status */
	private $isConnected = FALSE;

	/** @var PDOStatement */
	private $stmt = NULL;

	/**  @var string The SQL string to be used */
	private $sql = '';

	/** @var array Stored results from any READ query */
	private $result = [];

	/** @var array Store errors for external usage */
	private $errors = [];





	/**
	 * BringPDOSingleton constructor.
	 *
	 * @param $database
	 * @param $user
	 * @param $password
	 * @param $charset
	 * @param $host
	 *
	 * @throws VgaDatabaseException
	 */
	function __construct ( $database, $user, $password, $host = "localhost", $charset = "utf-8" ) {

		$this->database = $database;
		$this->user     = $user;
		$this->password = $password;
		$this->charset  = $charset;
		$this->host     = $host;

		$errmode = BPL_DEBUG ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT;

		$this->opts = [
			PDO::ATTR_PERSISTENT => TRUE,
			PDO::ATTR_ERRMODE    => $errmode
		];
	}


	/**
	 * Wrapper for the default calls to make a query for a single row read.
	 *
	 * This will return an single level array.
	 *
	 * ** PREPARED STATEMENTS **
	 * If you set a placeholder in the SQL string (as ':placeholder') you
	 * MUST define those names in the $data array. If this fails an
	 * exception will be thrown.
	 * The keys in dataarray must not be prepended with the ':', this will be done automatically.
	 * If placeholders are not used, the $data array can be ignored.
	 *
	 * Format:
	 * [ 'placeholder' => $value ]
	 *
	 * ** Options **
	 * Format: [ option => value ]
	 *
	 * Available options:
	 * 'fetchMode' : define using the FETCH_* values. FETCH_ASSOC is the default
	 *
	 *
	 * @param string $sql The MySQL string
	 * @param array  $data Use this with prepared statements.
	 * @param array  $opts As stated in options description.
	 *
	 * @return array The result of the query.
	 *
	 */
	public function readSingleRow ( string $sql, array $data = [], array $opts = [] ): array {

		$result = [];

		$fetchMode = isset( $opts[ 'fetchMode' ] ) ? $opts[ 'fetchMode' ] : self::FETCH_ASSOC;

		if ( $this->prepAndRun( $sql, $data ) ) {

			$result = $this->stmt->fetch( $fetchMode );

		}

		$this->reset( TRUE );

		return $result;
	}

	/**
	 * Run a SELECT query and return several rows.
	 *
	 * @param string $sql
	 * @param array  $data
	 * @param array  $opts
	 *
	 * @return array
	 */
	public function readRows ( string $sql, array $data, array $opts = [] ): array {

		if ( empty( $data ) ) throw new VgaDatabaseException( "Contains no data" );

		$result = [];

		$fetchMode = isset( $opts[ 'fetchMode' ] ) ? $opts[ 'fetchMode' ] : self::FETCH_ASSOC;

		$this->prepAndRun( $sql, $data );

		//todo finish this func!!!


	}

	/**
	 * Run INSERT query and return the last inserted ID(s).
	 *
	 * @param string $sql INSERT sql query.
	 * @param array  $data The data for prepared statements.
	 * @param bool   $multi if the query is to be run more than once.
	 *
	 * @return int | false
	 */
	public function runInsert ( $sql, $data, $multi = FALSE ) {

		$return = FALSE;

		if ( $this->prepAndRun( $sql, $data, $multi ) ) {
			$return = $this->lastInsertId();
		}

		$this->reset( TRUE );

		return $return;
	}

	/**
	 * @param string $sql
	 * @param array  $data
	 * @param bool   $multi
	 *
	 * @return bool
	 */
	public function updateRow ( string $sql, array $data, $multi = FALSE ) {

		$this->prepAndRun( $sql, $data, $multi );
		$this->reset();

		return TRUE;

	}


	/**
	 * @param bool $single if only one row is to be returned.
	 *
	 * @return array | bool
	 */
	public function getResult ( $single = FALSE ) {

		if ( empty( $this->result ) ) return FALSE;

		if ( $single ) {
			return $this->result[ 0 ];
		}
		else {
			return $this->result;
		}
	}


	/**
	 * @return int
	 */
	public function getRowCount () {
		return count( $this->result );
	}

	/**
	 * Wrapper for prepare and run functions. Returns true when complete.
	 * Will
	 *
	 * @param      $sql
	 * @param      $data
	 *
	 * @param bool $multi
	 *
	 * @return bool
	 */
	private function prepAndRun ( string $sql, array $data, $multi = FALSE ) {

		if ( ! $this->isConnected ) {
			$this->connect();
		}

		// Prepare or die!!
		return ( $this->prepareQuery( $sql ) && $this->runQuery( $data ) );

	}


	private function connect () {

		try {

			parent::__construct(
				'mysql:host=' . $this->host . ';'
				. 'dbname=' . $this->database . ';'
				. 'charset=' . $this->charset,
				$this->user,
				$this->password,
				$this->opts );

		}
		catch ( PDOException $e ) {
			error_log( __CLASS__ . '::' . __FUNCTION__ . ':: PDO exception' );
			throw new VgaDatabaseException( "Error when trying to connect to database", 0, $e );
		}

	}

	/**
	 * Readies the statement for execution.
	 *
	 * @param string $sql
	 *
	 * @return bool
	 * @throws VgaDatabaseException
	 */
	private function prepareQuery ( string $sql ): bool {

		$this->sql = $sql;

		try {

			$this->stmt = $this->prepare( $this->sql );

		}
		catch ( PDOException $e ) {

			error_log( __CLASS__ . '::' . __FUNCTION__ . ':: PDO exception : ' . $e->getMessage() );

			throw new VgaDatabaseException( "PDO Statment error.", 0, $e );
		}


		if ( ! $this->stmt ) {
			return FALSE;
		}
		else {
			return TRUE;
		}
	}


	/**
	 * Executes the query.
	 *
	 * The $statementKeyVals must be of same length as there are keys for this in the SQL string. If of another length
	 * execution will fail and an VgaDatabaseException will be thrown!
	 * The array keys will be prepended with ':', so don’t do that before passing it into this function.
	 *
	 * If the statement has not been prepared, this will throw VgaDatabaseException
	 * If execution fails, VgaDatabaseExeption will be thrown.
	 *
	 * True will be returned when complete
	 *
	 * @param array $statementKeyVals
	 *
	 * @throws VgaDatabaseException
	 *
	 * @return bool True when complete
	 */
	private function runQuery ( array $statementKeyVals = [] ): bool {

		$args = $this->prepareArgs( $statementKeyVals );

		// Check that the statement is prepared
		if ( empty( $this->stmt ) ) {
			error_log( __CLASS__ . '::' . __FUNCTION__ . ':: PDO exception : Tried to run a query without preparing a statment.' );

			throw new VgaDatabaseException( "PDO Statment error." );

		}

		// Execute the statement and check for errors in execution.
		if ( ! $this->stmt->execute( $args ) ) {
			error_log( __CLASS__ . '::' . __FUNCTION__ . ':: PDO exception : Tried to run a query without preparing a statment.' );

			throw new VgaDatabaseException( "PDO Statment error." );
		}

		// At this point all is good, so return a true.
		return TRUE;

	}


	/**
	 * Prepend keys with ':'
	 *
	 * @param array $aColVals
	 *
	 * @return array
	 */
	private function prepareArgs ( array $aColVals ): array {

		$args = [];

		foreach ( $aColVals as $col => $val ) {
			$args[ ":" . $col ] = $val;
		}

		return $args;
	}


	/**
	 * @param bool $fullReset
	 *
	 * @return bool
	 */
	private function reset ( bool $fullReset = FALSE ): bool {

		$this->result = NULL;

		if ( $fullReset ) {
			$this->sql    = '';
			$this->stmt   = NULL;
			$this->errors = [];
		}

		return TRUE;
	}


}
