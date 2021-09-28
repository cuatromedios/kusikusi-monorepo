# Kusikusi Website Controllers for Laravel
> This is a read-only repository, splitted from the monorepo at github.com/cuatromedios/kusikusi

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/kusikusi/website.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/kusikusi/website.svg?style=flat-square)](https://packagist.org/packages/kusikusi/website)

## Install

This package complements kusikusi/models package, that should be required first.

```
composer require kusikusi/website:dev-master
```

## Usage
### Publish the assets
- ##### Publish all the assets ...
  ```shell
  php artisan vendor:publish --provider="Kusikusi\WebsiteServiceProvider"
  ```

- ##### Or Publish individual Assets
  Configuration
  ```shell
  php artisan vendor:publish --provider="Kusikusi\WebsiteServiceProvider" --tag="config"
  ```

  HtmlController
  ```shell
  php artisan vendor:publish --provider="Kusikusi\WebsiteServiceProvider" --tag="controller"
  ```

### Migrations
```shell
php artisan migrate
```

### Seeds

#### An administrator user
```shell
php artisan db:seed --class=AdminSeeder
```

#### One of the website templates
```shell
php artisan db:seed --class=EmptyWebsiteSeeder
```
```shell
php artisan db:seed --class=BlogSeeder
```

### Routes
This Kusikusi Website Package, has a "catch-all" route. As this route may interfere with other application routes, this should **not** be automatically loaded. So, you will need to:

In your `composer.json` file, define it as not-discover:
```json
"extra": {
  "laravel": {
    "dont-discover": ["kusikusi/website"]
  }
}
```

In your application `config/app.php` configuration file, add the provider **at the end** of the providers array

```php
Kusikusi\WebsiteServiceProvider::class
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
- [All Contributors](https://github.com/kusikusi/website/contributors)

## Security
If you discover any security-related issues, please email dev@cuatromedios instead of using the issue tracker.

## License
The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.
