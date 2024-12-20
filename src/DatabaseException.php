<?php declare(strict_types=1);

namespace Kingsoft\Db;

/**
 * DatabaseException
 */
class DatabaseException extends \Exception {
	const ERROR_START = 0x2000; // Vieww errors start here
	const ERR_CONNECTION = DatabaseException::ERROR_START+0;
	const ERR_STATEMENT = DatabaseException::ERROR_START+1;
	const ERR_EXCECUTE = DatabaseException::ERROR_START+2;
	private static $messageEN = array(
		DatabaseException::ERR_CONNECTION => "Could not connect to database",
		DatabaseException::ERR_STATEMENT => 'Statement error: %s',
		DatabaseException::ERR_EXCECUTE => 'Statement execute error: %s'
	);
			
	/**
	 * __construct
	 */
	public function __construct (int $code, ?\Exception $previous = NULL, ?string $message = NULL )
	{
		if( $message ){
			parent::__construct( sprintf(self::$messageEN[$code], $message), $code, $previous );
		 } else {
			parent::__construct( self::$messageEN[$code], $code, $previous );
		}
	}
	
	static function createStatementException( \PDO $connection, ?string $message = NULL ) {
		$errorInfo = $connection->errorInfo();
		return new DatabaseException( DatabaseException::ERR_STATEMENT, NULL,
			$message ? sprintf( $message, $errorInfo[2] ) : $errorInfo[2] );
	}

	static function createExecutionException( \PDOStatement $stmt, ?string $message = NULL ) {
		$errorInfo = $stmt->errorInfo();
		return new DatabaseException( DatabaseException::ERR_EXCECUTE, NULL,
			$message ? sprintf( $message, $errorInfo[2] ) : $errorInfo[2] );
	}
	
	/**
	 * getMessageLang - Get message for specified language
	 *
	 * @param  string $lang
	 * @return string
	 */
	final function getMessageLang(string $lang = 'EN'): string
	{
		return self::$messageEN[$this->code];
	}
};