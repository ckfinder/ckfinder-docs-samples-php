CKFinder DiskQuota plugin sample
================================

This plugin sample illustrates usage of CKFinder events to set disk storage limit per user.

Please notice this is **not** a fully functional plugin. The quota checking method `isQuotaAvailable()` needs custom implementation.

## Configuration options

To set quota add following option to main CKFinder config file (usually named `config.php`):

```php
// ...
$config['DiskQuota'] = [
    'userQuota' => '200M'
],
```

The quota limit can be defined using [PHP shorthand byte values](http://php.net/manual/pl/faq.using.php#faq.using.shorthandbytes).
