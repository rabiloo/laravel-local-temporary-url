# Laravel Local Temporary Url

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rabiloo/laravel-local-temporary-url.svg)](https://packagist.org/packages/rabiloo/laravel-local-temporary-url)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/rabiloo/laravel-local-temporary-url/run-tests.yml?branch=master&label=tests)](https://github.com/rabiloo/laravel-local-temporary-url/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/rabiloo/laravel-local-temporary-url/check-styling.yml?branch=master&label=code%20style)](https://github.com/rabiloo/laravel-local-temporary-url/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/rabiloo/laravel-local-temporary-url.svg)](https://packagist.org/packages/rabiloo/laravel-local-temporary-url)

Provides `temporaryUrl()` method for local filesystem driver

![Laravel Local Temporary Url](https://banners.beyondco.de/Local%20Temporary%20Url.png?theme=light&packageManager=composer+require&packageName=rabiloo%2Flaravel-local-temporary-url&pattern=brickWall&style=style_1&description=Provides+%60temporaryUrl%60+method+for+local+filesystem+driver&md=1&showWatermark=0&fontSize=100px&images=clock)

> **NOTE:** Laravel 11 comes with native support for this feature. See https://laravel.com/docs/11.x/filesystem#temporary-urls
> This package provides the same feature for Laravel 9+ versions.

## Installation

You can install the package via composer:

```bash
composer require rabiloo/laravel-local-temporary-url
```

## Usage

Enable by config (See https://laravel.com/docs/11.x/filesystem#enabling-local-temporary-urls )

```php
# config/filesystems.php

return [
    'disks' => [
        // 
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
            'serve' => true, // Enable file server, default URL is `/local/temp/{path}?expires=xxx&signature=xxx`
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',   // Enable file server with `visibility` = `public` 
            'throw' => false,
        ],

        // Custom local disk
        'local2' => [
            'driver' => 'local',
            'root' => storage_path('app/local2'),
            'throw' => false,
            'serve' => true,        // Enable file server
            'url' => 'local2/tmp',  // The URL will be `/local2/tmp/{path}?expires=xxx&signature=xxx`
        ],

        //...
    ],
];
```

Make temporary URL

```php
use Illuminate\Support\Facades\Storage;

$url = Storage::disk('local')->temporaryUrl($path, now()->addSeconds(120));
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Oanh Nguyen](https://github.com/oanhnn)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
