# Kusikusi Forms Controllers 
> This is a read-only repository, splitted from the monorepo at github.com/cuatromedios/kusikusi

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/kusikusi/website.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/kusikusi/website.svg?style=flat-square)](https://packagist.org/packages/kusikusi/website)

## Install

This package complements kusikusi/models and kusikusi/website packages, that should be required first.

```
composer require kusikusi/website:dev-master
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
* Include the Laravel  CSRF token
* Include a hidden field with the `entity_id` of the current page
* The entity referenced should have a property `form` with specific params
  * `fields` the list of fields accepted by the form (other field will be discarded)
  * `mail_to` an email address if you want to send the entry to an email address. You should have mail values configured in your Laravel project.

```html
<form action="/form" method="post">
    <input name="name" />
    <input name="email" type="email" />
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" name="entity_id" value="{{ $entity->id }}" />
    <button type="submit">Enviar</button>
</form>
```

```json
{
  "form": {
    "mail_to": "contact@example.com",
    "fields": {
      "name":{},
      "email":{}
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
