cd kusikusi
rm database/migrations/*create_entities_*
php artisan vendor:publish --provider="Kusikusi\ModelsServiceProvider"
php artisan vendor:publish --provider="Kusikusi\MediaServiceProvider"
php artisan vendor:publish --provider="Kusikusi\WebsiteServiceProvider"
