# Database wrapper
Singleton that uses `POD` and connects with the following options
 * FETCH_BOUND
 * ERRMODE_EXCEPTION
 * SET NAMES utf8

## Interface
 * getConnection static returns the single instance PDO object
 * throw DatabaseException

## Configure
Use kingsoft/Utils to include a settings file

```php
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/kingsoft/utils/settings.inc.php';
```

The settings file should include this
```ini
[db]
hostname=hostname
database=database
username=username
password=password
```


# Documenter

A simple database documenter generaring the code to create tables, view, procedure and functions. Make sure the DB user has the proper rights to create these otherwise the result will be empty

## Sample

```php
$dsn        = "mysql:host=" . SETTINGS['db']['hostname'] . ";dbname=" . SETTINGS['db']['database'];
$connection = new PDO(
    $dsn,
    SETTINGS['db']['username'],
    SETTINGS['db']['password'] );
$documentor = ( new \Documentor( $connection, SETTINGS['db']['database'] ) )
    ->do_tables()
    ->do_procedures()
    ->do_functions()
;
```

