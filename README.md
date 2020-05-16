# Kusikusi

Kusikusi is a Lumen based headless CMS with a head. A boilerplate for creating API-first applications based on hierarchical data, like the data found on most websites. Can be used as the backend for websites, web applications, mobile applications or platforms containing all of them. KusiKusi also ~~has~~ (will have) tools to build static websites. 

Kusikusi is not reinventing the wheel, it is built on top of Lumen Framework by Laravel, so, it can be deployed on almost any hosting provider using PHP and MySQL. KusiKusi has its own way to organize models and its relations, mainly tree based relations.

### Installation
Kusikusi boilerplate is based in [Lumen Framework](https://lumen.laravel.com/), you should be familiarized with Lumen or Laravel framework first.

0. You can use Composer Create project
    > TODO: Using https://getcomposer.org/doc/03-cli.md#create-project
1. First install the dependencies
   ```shell script
   composer install
   ```
2. Rename the `.env.example` file to `.env   
3. Generate an application key. Kusikusi includes [Lumen Generator](https://github.com/flipboxstudio/lumen-generator) so you can run this command to generate a applicatio key
   ```shell script
   php artisan key:generate
   ```
4. Configure the database connection. In your .env file, filling the `DB_*` configuration options, dont forget to set the desired APP_TIMEZONE
5. Run the migrations 
   ```shell script
   php artisan migrate
   ```

### Running

You can run Kusikusi as any other PHP application, for example you can run it using the PHP internal web server, and use a provided router:

```shell script
php -S localhost:8000 -t public public/phprouter.php
```

## Official Documentation

Documentation for the framework can be found on TBD.

## License

Kusikusi is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
