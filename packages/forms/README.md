# Kusikusi Forms Controllers 
> This is a read-only repository, splitted from the monorepo at [github.com/cuatromedios/kusikusi-monorepo](https://github.com/cuatromedios/kusikusi-monorepo)

> For the Laravel starter kit visit [github.com/cuatromedios/kusikusi](https://github.com/cuatromedios/kusikusi)
> 
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/kusikusi/website.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/kusikusi/website.svg?style=flat-square)](https://packagist.org/packages/kusikusi/website)

## Install

This package complements kusikusi/models and kusikusi/website packages, that should be required first.

```
composer require kusikusi/forms
```

## Usage
### Publish the assets
- ##### Publish all the assets ...
  ```shell
  php artisan vendor:publish --provider="Kusikusi\FormServiceProvider"
  ```

- ##### Or Publish individual Assets
  Configuration
  ```shell
  php artisan vendor:publish --provider="Kusikusi\FormServiceProvider" --tag="config"
  ```

### Migrations
```shell
php artisan migrate
```

### Receiving forms
* Any form you want to be processed by Kusikusi Forms, set the action to `/form` and method to `post`
* Include the Laravel  [CSRF token](https://laravel.com/docs/csrf)
* Include a hidden field named `entity_id` with the entity id value of the current page
  ```html
  <form action="/form" method="post">
      <input name="name" />
      <input name="email" type="email" />
      <input type="hidden" name="_token" value="{{ csrf_token() }}" />
      <input type="hidden" name="entity_id" value="{{ $entity->id }}" />
      <button type="submit">Enviar</button>
  </form>
  ```
* The entity referenced should have in the `properties` field, a property named `form` with specific params:
  * `fields` an object with the keys as the field names and values as the [validation string](https://laravel.com/docs/validation) for that field, if a field is not described here will be ignored.
  * `mail_to` an email address if you want to send the entry to an email address. You should have mail values configured in your Laravel project.
  ```json
  {
    "form": {
      "mail_to": "contact@example.com",
      "fields": {
        "name": {
          "validation": "required|max:50"
        },
        "email": {
          "validation": "required|email"
        }
      }
    }
  }
  ```

### Routes
This Kusikusi Forms Package, has a routes specific for form entries management

Receiving form entries

```http
POST /form
```

API endpoints to manage the entries.

```http
GET /formentries/
GET /formentries/{formentry_id}
PATCH GET /formentries/{formentry_id}
DELETE GET /formentries/{formentry_id}
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
