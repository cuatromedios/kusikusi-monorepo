<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class EntityContent extends Model
{
    protected $table = 'entities_contents';

    /**
     * To avoid "ambiguous" SQL errors Change the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'content_id';
    }

    protected $fillable = ['entity_id', 'lang', 'field', 'text'];
    protected $hidden = array('created_at', 'updated_at');

    /**
     * RELATIONS
     */
    
    /**
     * @return Kusikusi\Models\Entity
     */
    public function entity($lang = null) {
        return $this->belongsTo('Kusikusi\Models\Entity', 'entity_id', 'id');
    }
    protected $touches = ['entity'];

    /**
     * Create multiple contents from an array
     */
    public static function createFromArray($entity_id, $contents) 
    {
        foreach($contents as $content){
            EntityContent::create([
                "entity_id" => $entity_id,
                "lang" => $content['lang'],
                "field" => $content['field'],
                "text" => $content['text'],
            ]);
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $entityContent) {
            self::where('entity_id', $entityContent->entity_id)->where('lang', $entityContent->lang)->where('field', $entityContent->field)->delete();
        });
        static::saved(function (Model $entityContent) {
            if ($entityContent->field === 'slug' && config('kusikusi_models.create_routes_from_slugs', false) === true){
                EntityRoute::createFromSlug($entityContent->entity_id, $entityContent->lang, $entityContent->text);
            }
        });
    }

}
