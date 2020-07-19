# Kusikusi

Kusikusi is a Lumen based headless CMS with a head. A boilerplate for creating API-first applications based on hierarchical data, like the data found on most websites. Can be used as the backend for websites, web applications, mobile applications or platforms containing all of them. KusiKusi also ~~has~~ (will have) tools to build static websites. 

Kusikusi is not reinventing the wheel, it is built on top of Lumen Framework by Laravel, so, it can be deployed on almost any hosting provider using PHP and MySQL. KusiKusi has its own way to organize models and its relations, mainly tree based relations.

### Installation
Kusikusi boilerplate is based in [Lumen Framework](https://lumen.laravel.com/), you should be familiarized with Lumen or Laravel framework first.

1. You can use Composer to easily create a new Kusikusi based project
    ```shell script
    composer create-project cuatromedios/kusikusi name-of-your-project
    ```
2. Or if you prefer, you can download or clone the project and install the dependencies with `composer install` and then rename the `.env.example` file to `.env   
3. Generate an application key. Kusikusi includes [Lumen Generator](https://github.com/flipboxstudio/lumen-generator) so you can run this command to generate a application key
   ```shell script
   php artisan key:generate
   ```
4. Configure the APP_URL env variable. In your .env file to point to your website or application url. This will be used for canonical and alternate urls and social share assets
5. Configure the database connection.
    1. In your .env file, filling the `DB_*` configuration options
    2. Don't forget to set the desired APP_TIMEZONE
6. Open the `config/cms.php` configuration file, and change the `langs` property to match your needs. Even if your website is just one language, set it there, use two letters standard like `en` or a two letter plus the country like `en_US`. Even if it is just only one language, this setting is an array. So use values like `["en"]`, `["en_US"]`, `["en_US", "es_MX"]`, `["en", "fr", "es", "pr"]`, 
7. Run the migrations, include --seed to also generate the basic website structure. You can also uncomment the sample site seeder in `database/seed/DatabaseSeeder.php` if you want to create a website with fake content for testing purposes. 
   ```shell script
   php artisan migrate --seed
   ```
   Note this will create, and display in the console, an admin username and password.
8. **Running.** You can run Kusikusi as any other PHP application, for example you can run it using the PHP internal web server, and use a provided router:
    ```shell script
    php -S 127.0.0.1:8000 -t public public/phprouter.php
    ```
9. Admin interface: Point your browser to `127.0.0.1:8000/cms` to go the admin interface and use the credentials created in the migration / seeding to login
### Run Laravel Mix for CSS pre-processing, auto-refresh and browser-sync

If you want to take advantage of LaravelMix to compile your assets, like css preprocessors, and use Browser Sync to quickly test changes on both css and views, Kusikusi comes with preconfigured Larevl Mix. You need to have installed node and npm in your computer:

Run once
```shell script
npm install
```

Run Laravel Mix, watching for changes:

```shell script
npm run watch
```

By default, this  will transpile SCSS file `resources/views/styles/main.scss` to `public/styles` and read from the PHP server already running in `http://127.0.0.1:8000/` But you can modify this and other Mix configurations in `webpack.mix.js` file

To crete production ready assets:

```shell script
npm run prod
```

## Official Documentation

Documentation for the framework can be found on [kusikusi.cuatromedios.com](https://kusikusi.cuatromedios.com).

## Testings
You will need a database called `kusikusi4testing` or configure it on phpunit.xml
``` shell script
./vendor/bin/phpunit
```

## License

Kusikusi is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
