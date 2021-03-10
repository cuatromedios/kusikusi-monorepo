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
    public static function createFromSlug($entity_id, $lang, $slug) {
        $entity = Entity::withRoute($lang, 'main')->find($entity_id);
        $parent = Entity::withRoute($lang, 'main')->parentOf($entity_id)->first();
        if (config('kusikusi_models.create_routes_redirects', false)) {
            self::where('entity_id', $entity_id)->where('lang', $lang)->where('kind', 'main')->update(['kind' => 'permanent_redirect']);
        } else {
            self::where('entity_id', $entity_id)->where('lang', $lang)->where('kind', 'main')->delete();
        }
        $parent_path = '';
        if ($parent && $parent->route) {
            $parent_path = $parent->route->path;
            if (!$parent_path || $parent_path === '/') {
                $parent_path = '';
            }
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
