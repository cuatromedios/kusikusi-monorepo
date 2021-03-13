<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Kusikusi\Models\EntityRelation;
use Kusikusi\Models\Entity;

class Website extends Entity
{
    protected $contentFields = [ "title" ];
    protected $propertiesFields = [ "theme_color", "background_color" ];

    protected static function boot()
    {
        parent::boot();
        self::saved(function ($entity) {
            //self::recreateFavicons($entity);
        });
    }
    protected static function recreateFavicons($entity) {

        $faviconRelation = EntityRelation::select('relations.called_entity_id', 'entities.properties->format as format')
            ->where('caller_entity_id',$entity->id)
            ->where('kind', EntityRelation::RELATION_MEDIA)
            ->whereJsonContains('tags', 'favicon')
            ->leftJoin("entities", function ($join) {
                $join->on("relations.called_entity_id", "entities.id");
            })
            ->first();
        if ($faviconRelation) {
            $id = $faviconRelation->called_entity_id;
            $path =   $id . '/file.' . $faviconRelation->format;
            if (Storage::disk('media_original')->exists($path)) {

                $image = Image::canvas(192, 192)
                    ->insert(Image::make(storage_path("media/$path"))->resize(192, 192))
                    ->encode('png');
                Storage::disk('views_processed')->put("favicons/android-chrome-192x192.png", $image);

                $image = Image::canvas(512, 512)
                    ->insert(Image::make(storage_path("media/$path"))->resize(512, 512))
                    ->encode('png');
                Storage::disk('views_processed')->put("favicons/android-chrome-512x512.png", $image);

                $image = Image::canvas(180, 180, $entity->properties['background_color'])
                    ->insert(Image::make(storage_path("media/$path"))->resize(148, 148), 'center')
                    ->encode('png');
                Storage::disk('views_processed')->put("favicons/apple-touch-icon.png", $image);

                $image = Image::canvas(16, 16)
                    ->insert(Image::make(storage_path("media/$path"))->resize(16, 16))
                    ->encode('png');
                Storage::disk('views_processed')->put("favicons/favicon-16x16.png", $image);

                $image = Image::canvas(32, 32)
                    ->insert(Image::make(storage_path("media/$path"))->resize(32, 32))
                    ->encode('png');
                Storage::disk('views_processed')->put("favicons/favicon-32x32.png", $image);

                $image = Image::canvas(270, 270)
                    ->insert(Image::make(storage_path("media/$path"))->resize(126, 126), 'top', null, 50)
                    ->encode('png');
                Storage::disk('views_processed')->put("favicons/mstile-150x150.png", $image);

                $favicon = new \PHP_ICO(storage_path("media/$path"), [[48,48]]);
                $favicon->save_ico(sys_get_temp_dir()."/favicon.ico");
                Storage::disk('views_processed')->putFileAs("favicons", sys_get_temp_dir()."/favicon.ico", "favicon.ico");

                $favicon = new \PHP_ICO(storage_path("media/$path"), [[16,16]]);
                $favicon->save_ico(sys_get_temp_dir()."/favicon.ico");
                Storage::disk('views_processed')->putFileAs("", sys_get_temp_dir()."/favicon.ico", "favicon.ico");
            }
        }
        $socialRelation = EntityRelation::select('relations.called_entity_id', 'entities.properties->format as format')
            ->where('caller_entity_id',$entity->id)
            ->where('kind', EntityRelation::RELATION_MEDIA)
            ->whereJsonContains('tags', 'social')
            ->leftJoin("entities", function ($join) {
                $join->on("relations.called_entity_id", "entities.id");
            })
            ->first();
        if ($socialRelation) {
            $id = $socialRelation->called_entity_id;
            $path =   $id . '/file.' . $socialRelation->format;
            if (Storage::disk('media_original')->exists($path)) {
                $image = Image::canvas(1200, 1200, $entity->properties['background_color'])
                    ->insert(Image::make(storage_path("media/$path"))->fit(1200, 1200))
                    ->encode('png');
                Storage::disk('views_processed')->put("favicons/social.png", $image);
            }
        }
        $theme_color = isset($entity->properties['theme_color']) ? $entity->properties['theme_color'] : "#000000";
        $background_color = isset($entity->properties['theme_color']) ? $entity->properties['theme_color'] : "#ffffff";
        $browserconfig = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<browserconfig>
    <msapplication>
        <tile>
            <square150x150logo src=\"/favicons/mstile-150x150.png\"/>
            <TileColor>".strip_tags($theme_color)."</TileColor>
        </tile>
    </msapplication>
</browserconfig>
";
        $titleContent = EntityContent::select('text')
            ->where('entity_id', $entity->id)
            ->where('field', 'title')
            ->where('lang', config('cms.langs', [''])[0])
            ->first();
        $name = $titleContent ? $titleContent->text : '';
        Storage::disk('views_processed')->put("favicons/browserconfig.xml", $browserconfig);
        $webmanifest = [
            "name" => strip_tags($name),
            "short_name" => strip_tags($name),
            "icons" => [
                [
                    "src" => "/favicons/android-chrome-192x192.png",
                    "sizes" => "192x192",
                    "type" => "image/png"
                ],
                [
                    "src" => "/favicons/android-chrome-512x512.png",
                    "sizes" => "512x512",
                    "type" => "image/png"
                ]
            ],
            "theme_color" => $theme_color,
            "background_color" => $background_color,
            "display" => "standalone"
        ];
        Storage::disk('views_processed')->put("favicons/site.webmanifest", json_encode($webmanifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
    public static function clearStatic($entity_id = null) {
        $cleared = [];
        if ($entity_id) {
            $entity = Entity::with('routes')->find($entity_id);
            foreach ($entity->routes as $route) {
                if ($route->path !== '' && $route->path !== '/') {
                    Storage::disk('views_processed')->deleteDirectory($route->path, true);
                } else {
                    Storage::disk('views_processed')->delete('index.html');
                }
                Storage::disk('views_processed')->delete($route->path.'.html');
                $cleared[] = $route->path;
            }
            /* if ($entity->model === 'medium') {
                MediumModel::clearStatic($entity_id);
            } */
        } else {
            $directories = Storage::disk('views_processed')->directories(null, false);
            $files = Storage::disk('views_processed')->files(null, false);
            foreach ($directories as $directory) {
                if (!in_array($directory, ['styles', 'js', 'favicons', 'media', 'images'])) {
                    Storage::disk('views_processed')->deleteDirectory($directory, true);
                    $cleared[] = $directory;
                }
            }
            foreach ($files as $file) {
                if (!in_array($file, ['robots.txt', 'favicon.ico', 'sitemap.xml'])) {
                    Storage::disk('views_processed')->delete($file);
                    $cleared[] = $file;
                }
            }
            $cachedViews = storage_path('/framework/views/');
            $files = glob($cachedViews.'*');
            foreach($files as $file) {
                if(is_file($file) && !in_array($file, ['.gitignore'])) {
                    @unlink($file);
                }
            }
            self::createSitemaps();
        }
        return $cleared;
    }
    public static function recreateStatic() {
        // TODO: Develop this method, should even recreate the LaravelMix assets
        self::clearStatic();
        //MediumModel::clearStatic();
        self::createSitemaps();
        Storage::disk('views_processed')->delete('favicon.ico');
        Storage::disk('views_processed')->deleteDirectory('favicons');
        $website = self::find('website');
        self::recreateFavicons($website);
        return true;
    }
    public static function createSitemaps($lang = null) {
        $langs = config('cms.langs', ['']);
        $si = xmlwriter_open_memory();
        xmlwriter_set_indent($si, 1);
        $res = xmlwriter_set_indent_string($si, '  ');
        xmlwriter_start_document($si, '1.0', 'UTF-8');
        xmlwriter_start_element($si, 'sitemapindex');
        xmlwriter_start_attribute($si, 'xmlns');
        xmlwriter_text($si, 'http://www.sitemaps.org/schemas/sitemap/0.9');
        xmlwriter_end_attribute($si);
        $entities = Entity::select('id', 'model', 'entities.updated_at')->appendRoute($lang)->get();
        $models = config("cms.include_in_sitemap", false);
        foreach($langs as $lang) {
            self::createSitemap($lang);
            xmlwriter_start_element($si, 'sitemap');
            xmlwriter_start_element($si, 'loc');
            xmlwriter_text($si, env('APP_URL')."/sitemap_{$lang}.xml");
            xmlwriter_end_element($si);
            xmlwriter_start_element($si, 'lastmod');
            xmlwriter_text($si, Carbon::now()->setTimezone(env('APP_TIMEZONE', 'UTC'))->toW3cString());
            xmlwriter_end_element($si);
            xmlwriter_end_element($si);
        }
        xmlwriter_end_element($si);
        xmlwriter_end_document($si);
        Storage::disk('views_processed')->put("sitemap.xml", xmlwriter_output_memory($si));
    }
    public static function createSitemap($lang = null) {
        if (!$lang) $lang = config('cms.langs', [''])[0];
        $sm = xmlwriter_open_memory();
        xmlwriter_set_indent($sm, 1);
        $res = xmlwriter_set_indent_string($sm, '  ');
        xmlwriter_start_document($sm, '1.0', 'UTF-8');
        xmlwriter_start_element($sm, 'urlset');
        xmlwriter_start_attribute($sm, 'xmlns:xsi');
            xmlwriter_text($sm, 'http://www.w3.org/2001/XMLSchema-instance');
            xmlwriter_end_attribute($sm);
        xmlwriter_start_attribute($sm, 'xsi:schemaLocation');
            xmlwriter_text($sm, 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
            xmlwriter_end_attribute($sm);
        xmlwriter_start_attribute($sm, 'xmlns');
            xmlwriter_text($sm, 'http://www.sitemaps.org/schemas/sitemap/0.9');
            xmlwriter_end_attribute($sm);
        $entities = Entity::select('id', 'model', 'entities.updated_at')->isPublished()->appendRoute($lang)->get();
        $models = config("cms.include_in_sitemap", false);
        foreach($entities as $entity) {
            if ($models === false) {
                $include = $entity->model !== 'medium';
            } else {
                $include = in_array($entity->model, $models);
            }
            if ($entity->route && $entity->route !== '' && $include) {
                xmlwriter_start_element($sm, 'url');
                xmlwriter_start_element($sm, 'loc');
                xmlwriter_text($sm, env('APP_URL').$entity->route);
                xmlwriter_end_element($sm);
                xmlwriter_start_element($sm, 'lastmod');
                xmlwriter_text($sm, Carbon::make($entity->updated_at)->setTimezone(env('APP_TIMEZONE', 'UTC'))->toW3cString());
                xmlwriter_end_element($sm);
                xmlwriter_end_element($sm);
            }
        }
        xmlwriter_end_element($sm);
        xmlwriter_end_document($sm);
        Storage::disk('views_processed')->put("sitemap_{$lang}.xml", xmlwriter_output_memory($sm));
    }
}