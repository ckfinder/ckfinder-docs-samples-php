# CKFinder 3 DiskQuota Plugin Sample

This sample plugin [illustrates the usage of CKFinder events](https://ckeditor.com/docs/ckfinder/ckfinder3-php/howto.html#howto_disk_quota) to set disk storage limit per user.

Please notice this is **not** a fully functional plugin. The quota checking method `isQuotaAvailable()` needs custom implementation.

## Configuration Options

To set the quota, add the following option to main CKFinder configuration file (usually named `config.php`):

```php
// ...
$config['DiskQuota'] = [
    'userQuota' => '200M'
];
```

The quota limit can be defined using [PHP shorthand byte values](http://php.net/manual/pl/faq.using.php#faq.using.shorthandbytes).
