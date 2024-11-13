# Restrict access to specific IP addresses.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/digitalcorehub/laravel-ip-restriction.svg?style=flat-square)](https://packagist.org/packages/digitalcorehub/laravel-ip-restriction)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/digitalcorehub/laravel-ip-restriction/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/digitalcorehub/laravel-ip-restriction/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/digitalcorehub/laravel-ip-restriction/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/digitalcorehub/laravel-ip-restriction/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/digitalcorehub/laravel-ip-restriction.svg?style=flat-square)](https://packagist.org/packages/digitalcorehub/laravel-ip-restriction)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.


## Installation

You can install the package via composer:

```bash
composer require digitalcorehub/laravel-ip-restriction
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-ip-restriction-config"
```

This is the contents of the published config file:

```php
return [
    'allowed_ips' => [
        "127.0.0.1", // Localhost
        // Other IPs
    ],
];
```

## Usage

```php
Route::middleware(['restrict_to_ip'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    });
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [DigitalCoreHub](https://github.com/DigitalCoreHub)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
