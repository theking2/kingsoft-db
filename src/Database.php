<?php

declare(strict_types=1);

namespace Kingsoft\Db;

if( !defined( 'SETTINGS_FILE' ) ) {
	define( 'SETTINGS_FILE', $_SERVER['DOCUMENT_ROOT'] . '/settings.ini' );
}
if( !defined( 'SETTINGS' ) ) {
	define( 'SETTINGS', parse_ini_file( SETTINGS_FILE, true ) );
}
/**
 * Database – Singelton class for database access
 */
final class Database
{
	/** @param Database $db */
	private static $db; // Instance of Database
	/** @param \PDO $connection */
	private \PDO $connection; // PDO Connection

	/**
	 * constructor
	 * the class is a singleton, and the constructor should not be called from
	 * the outside, hence private
	 */
	private function __construct()
	{
		$dsn = SETTINGS['db']['dsn'] ??
			'mysql:host=' . SETTINGS['db']['server'] . ';dbname=' . SETTINGS['db']['dbname'];

		try {
			$this->connection = new \PDO(
				$dsn,
				SETTINGS['db']['dbuser'],
				SETTINGS['db']['dbpass'],
				[ 
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_BOUND,
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT,
					\PDO::ATTR_EMULATE_PREPARES => false
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
		return new DatabaseException( 0x2100, null, $this->connection->errorInfo()[2] );
	}
}