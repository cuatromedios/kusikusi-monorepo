# Kusikusi

Kusikusi is a Lumen based headless CMS with a head. A boilerplate for creating API-first applications based on hierarchical data, like the data found on most websites. Can be used as the backend for websites, web applications, mobile applications or platforms containing all of them. KusiKusi also ~~has~~ (will have) tools to build static websites. 

Kusikusi is not reinventing the wheel, it is built on top of Lumen Framework by Laravel, so, it can be deployed on almost any hosting provider using PHP and MySQL. KusiKusi has its own way to organize models and its relations, mainly tree based relations.

### Installation
Kusikusi boilerplate is based in [Lumen Framework](https://lumen.laravel.com/), you should be familiarized with Lumen or Laravel framework first.

1. You can use Composer Create project
    > TODO: Using https://getcomposer.org/doc/03-cli.md#create-project
2. First install the dependencies
   ```shell script
   composer install
   ```
3. Rename the `.env.example` file to `.env   
4. Generate an application key. Kusikusi includes [Lumen Generator](https://github.com/flipboxstudio/lumen-generator) so you can run this command to generate a application key
   ```shell script
   php artisan key:generate
   ```
5. Configure the APP_URL env variable. In your .env file to point to your website or application url. This will be used for canonical and alternate urls and social share assets
6. Configure the database connection. In your .env file, filling the `DB_*` configuration options, dont forget to set the desired APP_TIMEZONE
7. Open the `config/cms.php` configuration file, and change the `langs` property to match your needs. Even if your website is just one language, set it there, use two letters standard like `en` or a two letter plus the country like `en_US`. Even if it is just only one language, this setting is an array. So use values like `["en"]`, `["en_US"]`, `["en_US", "es_MX"]`, `["en", "fr", "es", "pr"]`, 
8. Run the migrations, include --seed to also generate the basic website structure. You can also uncomment the sample site seeder in `database/seed/DatabaseSeeder.php` if you want to create a website with fake content for testing purposes. 
   ```shell script
   php artisan migrate --seed
   ```
9. The website should be ready to run   

### Running

You can run Kusikusi as any other PHP application, for example you can run it using the PHP internal web server, and use a provided router:

```shell script
php -S localhost:8000 -t public public/phprouter.php
```
### Run Laravel Mix for CSS pre-processing, auto-refresh and browser-sync

If you want to take advante of LaravelMix to compile your assets, like css preprocessors, and use Browser Sync to quickly test changes on both css and views, Kusikusi comes with preconfigured Larevl Mix. You need to have installed node and npm in your computer:

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

Documentation for the framework can be found on TBD.

## Testings
``` shell script
./vendor/bin/phpunit
```

## License

Kusikusi is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
