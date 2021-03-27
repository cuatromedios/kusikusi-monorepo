# Kusikusi Media Models and Controllers for Laravel
> This is a read-only repository, splitted from the monorepo at github.com/cuatromedios/kusikusi

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/kusikusi/media.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/kusikusi/media.svg?style=flat-square)](https://packagist.org/packages/kusikusi/media)

## Install
```
composer require kusikusi/media:dev-master
```

## Usage
### Publish the assets
- ##### Publish all the assets

  ```shell
  php artisan vendor:publish --provider="Kusikusi\MediaServiceProvider"
  ```

- ##### Or publish individual assets

  Config files
  ```shell
  php artisan vendor:publish --provider="Kusikusi\MediaServiceProvider" --tag="config"
  ```

  Route file
  ```shell
  php artisan vendor:publish --provider="Kusikusi\MediaServiceProvider" --tag="routes"
  ```

  Api Route file
  ```shell
  php artisan vendor:publish --provider="Kusikusi\MediaServiceProvider" --tag="api_routes"
  ```

### Include the routes
Include the needed route files in your `RouteServiceProvider.php` file, in the boot method:

```php
Route::middleware('web')
    ->group(base_path('routes/kusikusi_media.php'));
```

```php
Route::middleware('api')
    ->group(base_path('routes/kusikusi_media_api.php'));
```

> Don't forget to secure the routes!

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