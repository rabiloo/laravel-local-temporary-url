# This is my package Laravel Local Temporary Url

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rabiloo/laravel-local-temporary-url.svg?style=flat-square)](https://packagist.org/packages/rabiloo/laravel-local-temporary-url)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/rabiloo/laravel-local-temporary-url/Tests?label=tests)](https://github.com/rabiloo/laravel-local-temporary-url/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/rabiloo/laravel-local-temporary-url/Check%20&%20fix%20styling?label=code%20style)](https://github.com/rabiloo/laravel-local-temporary-url/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/rabiloo/laravel-local-temporary-url.svg?style=flat-square)](https://packagist.org/packages/rabiloo/laravel-local-temporary-url)

---
This package can be used as to scaffold a framework agnostic package. Follow these steps to get started:

1. Press the "Use template" button at the top of this repo to create a new repo with the contents of this skeleton
2. Run "php ./configure.php" to run a script that will replace all placeholders throughout all the files
3. Have fun creating your package.
---

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require rabiloo/laravel-local-temporary-url
```

## Usage

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
