# kusikusi-models

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/cuatromedios/kusikusi-models.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/cuatromedios/kusikusi-models.svg?style=flat-square)](https://packagist.org/packages/cuatromedios/kusikusi-models)

<a name="usage"></a>
## Usage
##### Require
```shell
composer require kusikusi/models
```

##### Export the config file
```shell
php artisan vendor:publish --provider="Kusikusi\ModelsServiceProvider" --tag="config"
```
##### Export the migrations
```shell
php artisan vendor:publish --provider="Kusikusi\ModelsServiceProvider" --tag="migrations"
```
> Please note the migrations have commented lines you could use to define foreing keys to user tables

##### Run the migrations
```shell
php artisan migrate
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

- [Cuatromedios](https://github.com/cuatromedios)
- [All Contributors](https://github.com/cuatromedios/kusikusi-models/contributors)

## Security
If you discover any security-related issues, please email dev@cuatromedios.com instead of using the issue tracker.

## License
The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.