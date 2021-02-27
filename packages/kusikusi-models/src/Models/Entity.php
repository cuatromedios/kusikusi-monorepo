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
use Kusikusi\Relations\PluckedHasMany;
use Kusikusi\Models\Traits\UsesShortId;
use Kusikusi\Models\EntityRelation;
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

    protected $guarded = ['created_at', 'updated_at', 'version', 'version_tree', 'version_relations', 'version_full'];
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
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'unpublished_at' => 'datetime',
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
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate($date)
    {
        return $date->format('Y-m-d\TH:i:sP');
    }
    

    /**
     * RELATIONS
     */

    public function entities_related($kind = null, $modelClassPath = 'Kusikusi\Models\Entity')
    {
        return $this->belongsToMany($modelClassPath, (new EntityRelation())->getTable(), 'caller_entity_id', 'called_entity_id')
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
        return $this->belongsToMany($modelClassPath, (new EntityRelation())->getTable(), 'called_entity_id', 'caller_entity_id')
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
    public function contents() {
        return $this->hasMany(EntityContent::class, 'entity_id', 'id');
    }
    public function content() {
        return $this->pluckedHasMany(EntityContent::class, 'entity_id', 'id', 'text', 'field');
    }
    public function archives() {
        return $this->hasMany(EntityArchive::class, 'entity_id', 'id');
    }

    /**
     * SCOPES
     */
    public function scopeWithContent($query, $lang = null, $fields = null)
    {
        return $query->with(['content' => function($q) use ($lang, $fields) {
            $q->when($lang !== null, function ($q) use ($lang) {
                return $q->where('lang', $lang);
            });
            $q->when($fields !== null, function ($q) use ($fields) {
                return $q->where('field', $fields);
            });
        }]);
    }

    /**
     * AGGREGATES
     */


    /**
     * PRIVATES AND OTHERS
     */

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
            $found_duplicate = self::find($id);
        } while (!!$found_duplicate);
        return $id;
    }
    /**
     * Define a one-to-many plucked relationship.
     *
     * @param  string  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pluckedHasMany($related, $foreignKey = null, $localKey = null, $pluckValue = null, $pluckKey= null) {
        $instance = $this->newRelatedInstance($related);
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->getKeyName();
        return $this->newPluckedHasMany(
            $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey, $pluckValue, $pluckKey
        );
    }
    /**
     * Instantiate a new PluckedHasMany relationship.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected function newPluckedHasMany(\Illuminate\Database\Eloquent\Builder $query, Model $parent, $foreignKey, $localKey, $pluckValue, $pluckKey)
    {
        return new PluckedHasMany($query, $parent, $foreignKey, $localKey, $pluckValue, $pluckKey);
    }

    /**
     * BOOT
     */
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
