# Configure
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
