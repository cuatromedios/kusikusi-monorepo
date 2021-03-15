# media

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/kusikusi/media.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/kusikusi/media.svg?style=flat-square)](https://packagist.org/packages/kusikusi/media)

## Install
`composer require kusikusi/media`

## Usage

##### Export the website route file for Laravel
```shell
php artisan vendor:publish --provider="Kusikusi\MediaServiceProvider" --tag="config"
```

##### Export the website route file for Laravel
```shell
php artisan vendor:publish --provider="Kusikusi\MediaServiceProvider" --tag="route"
```
Include the route in your RouteServiceProvider.php file, in the boot method, please remember the website routes should be ketp as the last ones:

```php
Route::middleware('web')
    ->group(base_path('routes/media.php'));
```


##### Export the website route file for Lumen
```shell
php artisan vendor:publish --provider="Kusikusi\MediaServiceProvider" --tag="route-lumen"
```

## Testing
Run the tests with:

``` bash
vendor/bin/phpunit
```

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Cuatromedios](https://github.com/kusikusi)
- [All Contributors](https://github.com/kusikusi/media/contributors)

## Security
If you discover any security-related issues, please email dev@cuatromedios.com instead of using the issue tracker.

## License
The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.