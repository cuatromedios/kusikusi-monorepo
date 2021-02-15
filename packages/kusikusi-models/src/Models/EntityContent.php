<?php

namespace Cuatromedios\Kusikusi\Models;

use Illuminate\Database\Eloquent\Model;

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
    protected $hidden = array('created_at', 'updated_at', 'entity_id', 'content_id');

    /**
     * RELATIONS
     */
    
    /**
     * @return Kusikusi\Models\Entity
     */
    public function entity($lang = null) {
        return $this->belongsTo('Kusikusi\Models\Entity', 'entity_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $entityContent) {
            self::where('entity_id', $entityContent->entity_id)->where('lang', $entityContent->lang)->where('field', $entityContent->field)->delete();
        });
    }

}
