Code samples for CKFinder PHP connector documentation
=====================

This repository contains ready-to-use code samples created for the CKFinder PHP connector documentation.

### Installation with Composer
```
composer require ckfinder/ckfinder-docs-samples-php
```

### Manual installation
1. Clone this repository (or download ZIP)
2. Move plugins to CKFinder `plugins` directory, so the structure looks like below:

```
plugins
├── DiskQuota
│   ├── DiskQuota.php
├── GetFileInfo
│   ├── GetFileInfo.php
└── UserActionsLogger
    └── UserActionsLogger.php
```

To enable plugins add their names to `plugins` configuration option in connector config file (by default `config.php`):

```php
$config['plugins'] = [
	'DiskQuota', 'GetFileInfo', 'UserActionsLogger'
];
```
    
License
-------
For license details see: [LICENSE.md](https://github.com/ckfinder/ckfinder-docs-samples-php/blob/master/LICENSE.md).
