# CKFinder UserActionsLogger Plugin Sample

This sample plugin [illustrates the usage of CKFinder events](http://docs.cksource.com/ckfinder3-php/howto.html#howto_logging_actions) to log selected user actions.

## Configuration Options

By default the log file is created inside the plugin directory. To change it,
please add the following option to the main CKFinder configuration file (usually named `config.php`):

```php
// ...
$config['UserActionsLogger'] = [
    'logFilePath' => '/custom/path/filename.log'
];
```

Do remember about changing file permissions to make the log file writable by PHP.

**Notice**: This plugin is a simplified demonstration. In a real plugin like this you should remember about
things like checking file permissions and concurrent file access by multiple scripts.
