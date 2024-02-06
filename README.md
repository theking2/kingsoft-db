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
