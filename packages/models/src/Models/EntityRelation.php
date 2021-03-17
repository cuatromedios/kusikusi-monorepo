<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EntityRelation extends Pivot
{

    const RELATION_ANCESTOR = 'ancestor';
    const RELATION_MEDIA = 'medium';
    const RELATION_UNDEFINED = 'relation';
    const RELATION_MENU = 'menu';

    protected $table = 'entities_relations';

    /**
     * To avoid "ambiguos" errors Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'relation_id';
    }

    protected $guarded = ['relation_id'];
    protected $fillable = ['caller_entity_id', 'called_entity_id', 'kind', 'position', 'depth', 'tags'];
    
    protected $casts = [
        'tags' => 'array'
    ];
    
    protected $hidden = ['created_at', 'updated_at'];

    public function caller_entity() {
        return $this->belongsTo('Kusikusi\Models\Entity', 'caller_entity_id', 'relation_id');
    }

    public function called_entity() {
        return $this->belongsTo('Kusikusi\Models\Entity', 'called_entity_id', 'relation_id');
    }

    /**
     * BOOT
     */
    protected static function boot(){
        parent::boot();
        static::saved(function (Pivot $entityRelation) {
            Entity::incrementEntityVersion($entityRelation['caller_entity_id']);
            Entity::incrementRelationsVersion($entityRelation['called_entity_id']);
        });

    }
}
