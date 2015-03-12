CKFinder UserActionsLogger plugin sample
========================================

This plugin sample illustrates usage of CKFinder events to log chosen user actions.

## Configuration options

By default the log file is created inside plugin directory. To change it,
please add following option to main CKFinder config file (usually named `config.php`):

```php
// ...
$config['UserActionsLogger'] = [
    'logFilePath' => '/custom/path/filename.log'
];
```

Please remember about changing file permissions to make the log file writable by PHP.

**Notice**: this plugin is a simplified demonstration. In a real plugin like this you should remember about
things like checking file permissions and concurrent file access by multiple scripts.
