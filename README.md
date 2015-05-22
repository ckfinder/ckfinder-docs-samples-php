# CKFinder 3 - Sample PHP Plugins

This repository contains ready-to-use code samples created for the [CKFinder PHP connector documentation](http://docs.cksource.com/ckfinder3-php/).

## Installation with Composer
```
composer require ckfinder/ckfinder-docs-samples-php
```

## Manual Installation
1. Clone this repository (or download ZIP).
2. Move downloaded plugins to the CKFinder `plugins` directory, so the structure looks like below:

```
plugins
├── DiskQuota
│   ├── DiskQuota.php
├── GetFileInfo
│   ├── GetFileInfo.php
└── UserActionsLogger
    └── UserActionsLogger.php
```

To enable plugins, add their names to the [`plugins`](http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_plugins) configuration option in the connector configuration file (by default `config.php`):

```php
$config['plugins'] = [
	'DiskQuota', 'GetFileInfo', 'UserActionsLogger'
];
```
    
## License

Copyright (c) 2015, CKSource - Frederico Knabben. All rights reserved.
For license details see: [LICENSE.md](https://github.com/ckfinder/ckfinder-docs-samples-php/blob/master/LICENSE.md).
