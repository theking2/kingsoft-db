<?php

declare(strict_types=1);

namespace Kingsoft\Db;

if( !defined( 'SETTINGS_FILE' ) ) {
	define( 'SETTINGS_FILE', $_SERVER[ 'DOCUMENT_ROOT' ] . '/settings.ini' );
}
if( !defined( 'SETTINGS' ) ) {
	define( 'SETTINGS', parse_ini_file( SETTINGS_FILE, true ) );
}
/**
 * Singelton class for database access
 * The connection is a PDO object
 * Connection parameters are read from the SETTINGS['db'] array containing:
 * - hostname
 * - database
 * - username
 * - password
 * - dsn (optional)
 * 
 * The connection has the following options set:
 * - charset=utf8
 * - default fetch mode is FETCH_BOUND
 * - error mode is ERRMODE_EXCEPTION
 * 
 */
final class Database
{
	/** @param Database $db */
	private static ?Database $db = null; // Instance of Database
	/** @param \PDO $connection */
	private \PDO $connection; // PDO Connection

	/**
	 * constructor
	 * the class is a singleton, and the constructor should not be called from
	 * the outside, hence private
	 */
	private function __construct()
	{
		$dsn = SETTINGS[ 'db' ][ 'dsn' ] ??
			sprintf(
				'mysql:host=%s;dbname=%s;charset=utf8',
				SETTINGS[ 'db' ][ 'hostname' ],
				SETTINGS[ 'db' ][ 'database' ]
			);

		try {
			$this->connection = new \PDO(
				$dsn,
				SETTINGS[ 'db' ][ 'username' ],
				SETTINGS[ 'db' ][ 'password' ],
				[ 
					// \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_BOUND,
					\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION
				]
			);
		} catch ( \PDOException $e ) {
			throw new DatabaseException( DatabaseException::ERR_CONNECTION, null, $e->getMessage() );
		}
	}

	/**
	 * Retrieve the connection object
	 * construct the first time
	 * @return \PDO
	 */
	public static function getConnection(): \PDO
	{
		if( static::$db == null ) {
			static::$db = new Database();
		}
		return static::$db->connection;
	}

	/**
	 * create a customized Exception
	 * @return DatabaseException
	 */
	public function getException(): DatabaseException
	{
		return new DatabaseException( 0x2100, null, $this->connection->errorInfo()[ 2 ] );
	}
}
