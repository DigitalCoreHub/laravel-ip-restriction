# Restrict access to specific IP addresses.


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
