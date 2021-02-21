<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use PUGX\Shortid\Shortid;
use Ankurk91\Eloquent\BelongsToOne;
use Kusikusi\Extensions\EntityCollection;
use Kusikusi\Models\Traits\UsesShortId;
use Kusikusi\Exceptions\DuplicatedEntityIdException;
use Kusikusi\Casts\Json;
use Illuminate\Support\Carbon;

class Entity extends Model
{
    use BelongsToOne;
    use HasFactory;
    use UsesShortId;
    use SoftDeletes;

    protected $table = 'entities';

    protected $guarded = ['created_at', 'updated_at'];
    protected $contentFields = [];
    protected $touches = ['entities_relating'];
    protected $propertiesFields = [];
    private $storedContents = [];
    private $storedRelations = [];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = []) {
        return new EntityCollection($models);
    }

    /**
    * @var array A list of columns from the entities tables and other joins needs to be casted
    */
    protected $casts = [
        'properties' => Json::class,
        'tags' => 'array',
        'child_relation_tags' => 'array',
        'descendant_relation_tags' => 'array',
        'siblings_relation_tags' => 'array',
        'media_tags' => 'array',
        'relation_tags' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Kusikusi\Database\Factories\EntityFactory::new();
    }

    /**
     * ACCESORS AND MUTATORS
     */
    public function getPublishedAtAttribute($value)
    {
        return isset($value) ? Carbon::make($value)->format('Y-m-d\TH:i:sP') : null;
    }
    public function getUnpublishedAtAttribute($value)
    {
        return isset($value) ? Carbon::make($value)->format('Y-m-d\TH:i:sP') : null;
    }
    public function setPublishedAtAttribute($value)
    {
        if ($value !== null) $this->attributes['published_at'] = Carbon::make($value)->setTimezone(env('APP_TIMEZONE', 'UTC'))->format('Y-m-d\TH:i:s');
    }
    public function setUnpublishedAtAttribute($value)
    {
        if ($value !== null) $this->attributes['unpublished_at'] = Carbon::make($value)->setTimezone(env('APP_TIMEZONE', 'UTC'))->format('Y-m-d\TH:i:s');
    }
    

    /**
     * RELATIONS
     */

    public function entities_related($kind = null, $modelClassPath = 'Kusikusi\Models\Entity')
    {
        return $this->belongsToMany($modelClassPath, EntityRelation::table, 'caller_entity_id', 'called_entity_id')
            ->using('Kusikusi\Models\EntityRelation')
            ->as('relation')
            ->withPivot('kind', 'position', 'depth', 'tags')
            ->when($kind, function ($q) use ($kind) {
                return $q->where('kind', $kind);
            })
            ->when($kind === null, function ($q) use ($kind) {
                return $q->where('kind', '!=', EntityRelation::RELATION_ANCESTOR);
            })
            ->withTimestamps();
    }
    public function entities_relating($kind = null, $modelClassPath = 'Kusikusi\Models\Entity') {
        return $this->belongsToMany($modelClassPath, EntityRelation::TABLE, 'called_entity_id', 'caller_entity_id')
            ->using('Kusikusi\Models\EntityRelation')
            ->as('relation')
            ->withPivot('kind', 'position', 'depth', 'tags')
            ->when($kind, function ($q) use ($kind) {
                return $q->where('kind', $kind);
            })
            ->when($kind === null, function ($q) use ($kind) {
                return $q->where('kind', '!=', EntityRelation::RELATION_ANCESTOR);
            })
            ->withTimestamps();
    }
    public function entity_relations() {
        return $this->hasMany(EntityRelation::class, 'caller_entity_id', 'id')
            ->where('kind', '!=', EntityRelation::RELATION_ANCESTOR);
    }
    public function contents($lang = null) {
        return $this->hasMany(EntityContent::class, 'entity_id', 'id')
            ->when($lang !== null, function ($q) use ($lang) {
                return $q->where('lang', $lang);
            });
    }
    public function archives() {
        return $this->hasMany(EntityArchive::class, 'entity_id', 'id');
    }

    /**
     * Returns the id of the instance, if none is defined, it creates one
     */
    private function getId() {
        if (!isset($this->id)) {
            $this->id = self::createId();
        }
        return $this->id;
    }
    private static function createId() {
        do {
            $id = (string) Shortid::generate(Config::get('cms.shortIdLength', 10));
            $found_duplicate = Entity::find($id);
        } while (!!$found_duplicate);
        return $id;
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $entity) {

            // Set a default id
            if (!isset($entity[$entity->getKeyName()])) {
                $entity->setAttribute($entity->getKeyName(), self::createId());
            } else {
                if (self::find($entity[$entity->getKeyName()])) {
                   throw new DuplicatedEntityIdException();
                }
                $entity->setAttribute($entity->getKeyName(), substr($entity[$entity->getKeyName()], 0, 32));
            }

            // Setting default values
            if (!isset($entity['model']))        { $entity['model'] = 'Entity'; }
            if (!isset($entity['view']))         { $entity['view'] = Str::snake($entity['model']); }
            if (!isset($entity['is_active']))    { $entity['is_active'] = true; }
            if (!isset($entity['properties']))   { $entity['properties'] = new \ArrayObject(); }
            if (!isset($entity['published_at'])) { $entity['published_at'] = Carbon::now(); }
            $entity['version'] = 1;
            $entity['version_tree'] = 1;
            $entity['version_relations'] = 1;
            $entity['version_full'] = 1;

        });
    }
}
