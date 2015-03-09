CKFinder DiskQuota plugin stub
==============================

This plugin sample shows usage of CKFinder events to set disk storage limit per user.

**Note**: This is **NOT** a fully functional plugin. The quota checking method `checkQuota()` needs custom implementation.

## Configuration options

To set custom quota add following option to main CKFinder config file (usually named `config.php`):

```php
// ...
$config['DiskQuota'] = array(
    'userQuota' => '200M'
),
```

The quota limit can be defined using [PHP shorthand byte values](http://php.net/manual/pl/faq.using.php#faq.using.shorthandbytes).
