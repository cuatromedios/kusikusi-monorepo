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

  Model files
  ```shell
  php artisan vendor:publish --provider="Kusikusi\MediaServiceProvider" --tag="model"
  ```
 
  Config files
  ```shell
  php artisan vendor:publish --provider="Kusikusi\MediaServiceProvider" --tag="config"
  ```

### Routes
The required routes are automatically loaded from the package

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
