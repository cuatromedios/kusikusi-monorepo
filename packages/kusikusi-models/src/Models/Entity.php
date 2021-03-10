<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PUGX\Shortid\Shortid;
use Ankurk91\Eloquent\BelongsToOne;
use Kusikusi\Extensions\EntityCollection;
use Kusikusi\Relations\PluckedHasMany;
use Kusikusi\Models\Traits\UsesShortId;
use Kusikusi\Models\EntityRelation;
use Kusikusi\Models\EntityArchive;
use Kusikusi\Models\EntityRoute;
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

    /* protected $guarded = ['created_at', 'updated_at']; */
    protected $fillable = ['id', 'model', 'view', 'properties', 'is_active', 'parent_entity_id', 'created_at', 'published_at', 'unpublished_at'];
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

    /**
     * Get the entities this entity is calling
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

    /**
     * Get the other entities calling this entity
     */ 
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
    /**
     * Get the records in the entities_relations table this entity is using
     */ 
    public function entity_relations() {
        return $this->hasMany(EntityRelation::class, 'caller_entity_id', 'id')
            ->where('kind', '!=', EntityRelation::RELATION_ANCESTOR);
    }
    /**
     * Get the raw contents related to this entity
     */ 
    public function contents() {
        return $this->hasMany(EntityContent::class, 'entity_id', 'id');
    }
    /**
     * Get the contents by field name (plucked)
     */ 
    public function content() {
        return $this->pluckedHasMany(EntityContent::class, 'entity_id', 'id', 'text', 'field');
    }
    /**
     * Get the archives of this entity
     */ 
    public function archives() {
        return $this->hasMany(EntityArchive::class, 'entity_id', 'id');
    }
    public function routes() {
        return $this->hasMany(EntityRoute::class, 'entity_id', 'id');
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
     * Scope a query to only include entities of a given modelId.
     *
     * @param  Builder $query
     * @param  mixed $modelId
     * @return Builder
     */
    public function scopeOfModel($query, $modelId)
    {
        // TODO: Accept array of model ids
        return $query->where('model', $modelId);
    }

    /**
     * Scope a query to only include published entities.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeIsPublished($query)
    {
        return $query->where('is_active', true)
            ->whereDate('published_at', '<=', Carbon::now()->setTimezone('UTC')->format('Y-m-d\TH:i:s'))
            ->where(function($query) {
                $query->whereDate('unpublished_at', '>', Carbon::now()->setTimezone('UTC')->format('Y-m-d\TH:i:s'))
                    ->orWhereNull('unpublished_at');
            })
            ->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include children of a given parent id.
     *
     * @param Builder $query
     * @param integer $entity_id The id of the parent entity
     * @param string $tag Filter by one tag
     * @return Builder
     * @throws \Exception
     */
    public function scopeChildrenOf($query, $entity_id, $tag = null)
    {
        $query->join('entities_relations as relation_children', function ($join) use ($entity_id, $tag) {
            $join->on('relation_children.caller_entity_id', '=', 'entities.id')
                ->where('relation_children.called_entity_id', '=', $entity_id)
                ->where('relation_children.depth', '=', 1)
                ->where('relation_children.kind', '=', EntityRelation::RELATION_ANCESTOR)
                ->when($tag, function ($q) use ($tag) {
                    return $q->whereJsonContains('relation_children.tags', $tag);
                });
            ;
        })
            ->addSelect('relation_children.position as child_relation_position')
            ->addSelect('relation_children.tags as child_relation_tags');
    }

    /**
     * Scope a query to only include the parent of the given id.
     *
     * @param Builder $query
     * @param number $entity_id The id of the parent entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeParentOf($query, $entity_id)
    {
        $query->join('entities_relations as relation_parent', function ($join) use ($entity_id) {
            $join->on('relation_parent.called_entity_id', '=', 'entities.id')
                ->where('relation_parent.caller_entity_id', '=', $entity_id)
                ->where('relation_parent.depth', '=', 1)
                ->where('relation_parent.kind', '=', EntityRelation::RELATION_ANCESTOR)
            ;
        })
            ->addSelect('relation_parent.depth as parent_relation_depth');
    }

    /**
     * Scope a query to only include ancestors of a given entity.
     *
     * @param Builder $query
     * @param number $entity_id The id of the parent entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeAncestorsOf($query, $entity_id)
    {
        $query->join('entities_relations as relation_ancestor', function ($join) use ($entity_id) {
            $join->on('relation_ancestor.called_entity_id', '=', 'entities.id')
                ->where('relation_ancestor.caller_entity_id', '=', $entity_id)
                ->where('relation_ancestor.kind', '=', EntityRelation::RELATION_ANCESTOR)
            ;
        })
            ->addSelect('relation_ancestor.depth as ancestor_relation_depth');
    }

    /**
     * Scope a query to only include descendants of a given entity id.
     *
     * @param Builder $query
     * @param number $entity_id The id of the  entity
     * @return Builder
     * @throws \Exception
     */
    public function scopeDescendantsOf($query, $entity_id, $depth = 99)
    {
        $query->join('entities_relations as relation_descendants', function ($join) use ($entity_id, $depth) {
            $join->on('relation_descendants.caller_entity_id', '=', 'entities.id')
                ->where('relation_descendants.called_entity_id', '=', $entity_id)
                ->where('relation_descendants.kind', '=', EntityRelation::RELATION_ANCESTOR)
                ->where('relation_descendants.depth', '<=', $depth);
        })
            ->addSelect('relation_descendants.position as descendant_relation_position')
            ->addSelect('relation_descendants.depth as descendant_relation_depth')
            ->addSelect('relation_descendants.tags as descendant_relation_tags');
    }

    /**
     * Scope a query to only include descendants of a given entity id.
     *
     * @param Builder $query
     * @param number $entity_id The id of the  entity
     * @param string $tag Filter by tag in the relation with the parent
     * @return Builder
     * @throws \Exception
     */
    public function scopeSiblingsOf($query, $entity_id, $tag = null)
    {
        $parent_entity = Entity::find($entity_id);
        $query->join('entities_relations as relation_siblings', function ($join) use ($parent_entity, $tag) {
            $join->on('relation_siblings.caller_entity_id', '=', 'entities.id')
                ->where('relation_siblings.called_entity_id', '=', $parent_entity->parent_entity_id)
                ->where('relation_siblings.depth', '=', 1)
                ->where('relation_siblings.kind', '=', EntityRelation::RELATION_ANCESTOR)
                ->when($tag, function ($q) use ($tag) {
                    return $q->whereJsonContains('relation_siblings.tags', $tag);
                });
            ;
        })
            ->where('entities.id', '!=', $entity_id)
            ->addSelect('relation_siblings.position as siblings_relation_position')
            ->addSelect('relation_siblings.tags as siblings_relation_tags');
    }

    /**
     * Scope a query to only get entities being called by.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $entity_id The id of the entity calling the relations
     * @param  string $kind Filter by type of relation, if ommited all relations but 'ancestor' will be included
     * @param  string $tag Filter by tag in relation
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRelatedBy($query, $entity_id, $kind = null, $tag = null)
    {
        $query->join('entities_relations as related_by', function ($join) use ($entity_id, $kind, $tag) {
            $join->on('related_by.called_entity_id', '=', 'entities.id')
                ->where('related_by.caller_entity_id', '=', $entity_id)
                ->when($tag, function ($q) use ($tag) {
                    return $q->whereJsonContains('related_by.tags', $tag);
                });;
            if ($kind === null) {
                $join->where('related_by.kind', '!=', 'ancestor');
            } else {
                $join->where('related_by.kind', '=', $kind);
            }
        })->addSelect('related_by.relation_id as relation_id', 'related_by.kind as relation_kind', 'related_by.position as relation_position', 'related_by.depth as relation_depth', 'related_by.tags as relation_tags');
    }

    /**
     * Scope a query to only get entities calling.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $entity_id The id of the entity calling the relations
     * @param string $kind Filter by type of relation, if ommited all relations but 'ancestor' will be included
     * @param string $tag Filter by tag in relation
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    public function scopeRelating($query, $entity_id, $kind = null, $tag = null)
    {
        $query->join('entities_relations as relating', function ($join) use ($entity_id, $kind, $tag) {
            $join->on('relating.caller_entity_id', '=', 'entities.id')
                ->where('relating.called_entity_id', '=', $entity_id)
                ->when($tag, function ($q) use ($tag) {
                    return $q->whereJsonContains('relating.tags', $tag);
                });;
            if ($kind === null) {
                $join->where('relating.kind', '!=', 'ancestor');
            } else {
                $join->where('relating.kind', '=', $kind);
            }
        })->addSelect('relating.kind as relation_kind', 'relating.position as relation_position', 'relating.depth as relation_depth', 'relating.tags as relation_tags');
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
            $id = (string) Shortid::generate(Config::get('cms.short_id_length', 10));
            $found_duplicate = Entity::find($id);
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
     * Version Increments
     */
    /* private */ 
    public static function incrementEntityVersion($entity_id) {
        $e = DB::table('entities')
            ->where('id', $entity_id);
        $e->increment('version');
        $e->increment('version_full');
        self::incrementTreeVersion($entity_id);
        self::incrementRelationsVersion($entity_id);
    }
    private static function incrementTreeVersion($entity_id) {
        $ancestors = Entity::select('id')->ancestorsOf($entity_id)->get();
        if (!empty($ancestors)) {
            foreach ($ancestors as $ancestor) {
                $e = DB::table('entities')
                    ->where('id', $ancestor['id']);
                $e->increment('version_tree');
                $e->increment('version_full');
            }
        }
    }
    /* private */ 
    public static function incrementRelationsVersion($entity_id) {
        $relating = Entity::select('id')->relating($entity_id)->get();
        if (!empty($relating)) {
            foreach ($relating as $entityRelating) {
                $e = DB::table('entities')
                    ->where('id', $entityRelating['id']);
                $e->increment('version_relations');
                $e->increment('version_full');
                $ancestors = Entity::select('id')->ancestorsOf($entityRelating->id)->get();
                if (!empty($ancestors)) {
                    foreach ($ancestors as $ancestor) {
                        $e = DB::table('entities')
                            ->where('id', $ancestor['id']);
                        $e->increment('version_full');
                    }
                }
            }
        }
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
        static::saved(function (Model $entity) {
            $parentEntity = Entity::with('routes')->find($entity['parent_entity_id']);
            // Create the ancestors relations
            if ($parentEntity && isset($entity['parent_entity_id']) && $entity['parent_entity_id'] != NULL && $entity->isDirty('parent_entity_id')){
                EntityRelation::where("caller_entity_id", $entity->id)->where('kind', EntityRelation::RELATION_ANCESTOR)->delete();
                EntityRelation::create([
                    "caller_entity_id" => $entity->id,
                    "called_entity_id" => $parentEntity->id,
                    "kind" => EntityRelation::RELATION_ANCESTOR,
                    "depth" => 1
                ]);
                $depth = 2;
                $ancestors = Entity::select('id')->ancestorsOf($parentEntity->id)->orderBy('ancestor_relation_depth')->get();
                foreach ($ancestors as $ancestor) {
                    EntityRelation::create([
                        "caller_entity_id" => $entity->id,
                        "called_entity_id" => $ancestor->id,
                        "kind" => EntityRelation::RELATION_ANCESTOR,
                        "depth" => $depth
                    ]);
                    $depth++;
                }
            };
            // Update versions
            self::incrementEntityVersion($entity->id);
            // Setting routes
            /* Config::get('kusikusi_models.generate_routes', false);
            if(config('kusikusi_models.generate_routes') === true){
                EntityRoute::generateRoutes($entity, $parentEntity);
            } */
            // Setting archive version
            $config = Config::get('kusikusi_models.store_versions', false);
            if(config('kusikusi_models.store_versions') === true){
                EntityArchive::archive($entity->id, 'version');
            }
        });

    }
}
