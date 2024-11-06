<?php declare(strict_types=1);

namespace Kingsoft\Db;

define( 'SETTINGS', [ 
    'db' => [ 
        'hostname' => 'localhost',
        'database' => 'test',
        'username' => 'username',
        'password' => 'password'
    ]
]
);
class DatabaseTest extends \PHPUnit\Framework\TestCase
{
    public function testDatabase()
    {
        try {
            $db = Database::getConnection();
            $this->assertNull( $db );
        } catch ( DatabaseException $e ) {
            $this->assertInstanceOf( DatabaseException::class, $e );
        }
    }

}