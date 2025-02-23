<?php declare(strict_types=1);

namespace Kingsoft\Db;

readonly class Documenter
{
    private \PDO $connection;
    public function __construct( private Database $database, private string $dbname )
    {
        $this->connection = $database->getConnection();
    }

    public function do_table( string $name ): self
    {
        $query  = "SHOW CREATE TABLE `$name`";
        $result = $this->connection->query( $query, \PDO::FETCH_ASSOC );
        if( !is_dir( "./tables" ) ) {
            mkdir( "./tables", 0777, true );
        }
        foreach( $result as $row ) {
            echo "<li>$name\r\n";
            $table  = $row["Table"];
            $source = $row["Create Table"];
            $source = preg_replace( "/( ENGINE=\w* )/", "\r\n/* $1 */\r\n", $source );
            $fh     = fopen( "./tables/$name.sql", "w" );

            fwrite( $fh, "DROP TABLE IF EXISTS `$name`;\r\n\r\n" );
            fwrite( $fh, "DELIMITER #\r\n" );
            fwrite( $fh, str_replace( '\n', DIRECTORY_SEPARATOR, $source ) );
            fwrite( $fh, "#\r\n" );
            fwrite( $fh, "# DELIMITER ;\r\n" );
            fclose( $fh );
        }

        return $this;
    }
    public function do_view( string $name ): self
    {
        $query  = "SHOW CREATE VIEW `$name`";
        $result = $this->connection->query( $query, \PDO::FETCH_ASSOC );
        if( !is_dir( "./views" ) ) {
            mkdir( "./views", 0777, true );
        }
        foreach( $result as $row ) {
            echo "
    <li>$name\r\n";
            $view   = $row["View"];
            $source = $row["Create View"];
            $source = preg_replace( "/( DEFINER=`\w*`@`.?`) /", "\r\n/* $1 */\r\n", $source );
            $fh     = fopen( "./views/$name.sql", "w" );

            fwrite( $fh, "DROP VIEW IF EXISTS `$name`;\r\n\r\n" );
            fwrite( $fh, "DELIMITER #\r\n" );
            fwrite( $fh, str_replace( '\n', DIRECTORY_SEPARATOR, $source ) );
            fwrite( $fh, "#\r\n" );
            fwrite( $fh, "# DELIMITER ;\r\n" );
            fclose( $fh );
        }

        return $this;
    }
    public function do_routines( string $type, string $name ): self
    {
        $query  = "SHOW CREATE $type `$name`";
        $result = $this->connection->query( $query, \PDO::FETCH_ASSOC );
        if( !is_dir( "./$type" ) ) {
            mkdir( "./$type", 0777, true );
        }
        foreach( $result as $row ) {
            echo "
    <li>$name\r\n";
            if( $row["Create $type"] === null ) {
                echo "<p>Not found</p>";
                continue;
            }
            $proc   = $row[$type];
            $source = $row["Create $type"];
            $source = preg_replace( "/( DEFINER=`\w*`@`.?`) /", "\r\n/* $1 */\r\n", $source );
            $fh     = fopen( "./$type/$name.sql", "w" );

            fwrite( $fh, "DROP $type IF EXISTS `$name`;\r\n\r\n" );
            fwrite( $fh, "DELIMITER #\r\n" );
            fwrite( $fh, $source );
            fwrite( $fh, "#\r\n" );
            fwrite( $fh, "# DELIMITER ;\r\n" );
            fclose( $fh );
        }

        return $this;
    }

    public function do_procedures(): self
    {
        echo "<h1>Procedures</h1>
        <ul>";
        foreach( $this->connection->query( "SHOW PROCEDURE STATUS WHERE Db = '{$this->dbname}'", \PDO::FETCH_ASSOC) as $row ) {
            $this->do_routines( "Procedure", $row["Name"] );
        }
        echo "</ul>";

        return $this;
    }

    public function do_functions(): self
    {
        echo "<h1>Functions</h1>
        <ul>";
        foreach( $this->connection->query( "SHOW FUNCTION STATUS WHERE Db = '{$this->dbname}'", \PDO::FETCH_ASSOC ) as $row ) {
            $this->do_routines( "Function", $row["Name"] );
        }
        echo "</ul>";

        return $this;
    }
    public function do_tables(): self
    {
        echo "<h1>Tables</h1>
        <ul>";
        foreach( $this->connection->query( "SHOW FULL TABLES WHERE Table_type='BASE TABLE'", \PDO::FETCH_NUM ) as $row ) {
            $this->do_table( $row[0] );
        }
        echo "</ul>";
        echo "<h1>Views</h1>
        <ul>";
        foreach( $this->connection->query( "SHOW FULL TABLES WHERE Table_type='VIEW'", \PDO::FETCH_NUM ) as $row ) {
            $this->do_view( $row[0] );
        }
        echo "</ul>";

        return $this;
    }
}