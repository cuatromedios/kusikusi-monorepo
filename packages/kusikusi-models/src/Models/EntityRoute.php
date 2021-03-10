<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Model;


class EntityRoute extends Model
{

    protected $table = 'entities_routes';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'route_id', 'entity_id', 'entity_model'];
    protected $fillable = ['entity_id', 'path', 'entity_model', 'lang', 'kind'];
    protected $casts = ['default' => 'boolean'];

    /**
     * To avoid "ambiguous" SQL errors Change the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'route_id';
    }

    public function entity () {
        return $this->belongsTo('Kusikusi\Models\Entity');

    }

    // Create the automatic created routes
    public static function generateRoutes($entity, $entity_parent) {
        echo '<script type="text/javascript">' .
            'console.log(' . print_r($entity->properties) . ');</script>';
            consoleLog('Hello, console!');
        if (isset($entity->properties['slug'])) {
            EntityRoute::where('entity_id', $entity->id)->where('kind', 'main')->delete();
            foreach ($entity->properties['slug'] as $lang => $slug) {
                if ($entity_parent->routes->count()) {
                    foreach($entity_parent->routes as $route) {
                        if ($route->default && $route->lang === $lang) {
                            $parent_path = $route->path;
                            if ($parent_path === '/') {
                                $parent_path = '';
                            }
                            EntityRoute::create([
                                "entity_id" => $entity->id,
                                "entity_model" => $entity->model,
                                "path" => $parent_path."/".$slug,
                                "lang" => $lang,
                                "kind" => "main"
                            ]);
                        }
                    }
                } else {
                    EntityRoute::create([
                        "entity_id" => $entity->id,
                        "entity_model" => $entity->model,
                        "path" => "/".$slug,
                        "lang" => $lang,
                        "kind" => "main"
                    ]);
                }
            }
        }
    }

}
