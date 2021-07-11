<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityContent;
use Kusikusi\Models\EntityRoute;
use Kusikusi\Models\EntityRelation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ImportQueue implements ShouldQueue 
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const ID_RULE = 'string|min:1|max:32|regex:/^[A-Za-z0-9_-]{1,32}$/';
    const ID_RULE_WITH_FILTER = 'string|min:1|max:64|regex:/^[A-Za-z0-9_-]+:?[a-z0-9]*$/';
    const ENTITY_EXISTS = 'string|min:1|max:32|regex:/^[A-Za-z0-9_-]{1,32}$/|exists:entities,id';
    const MODEL_RULE = 'string|min:1|max:32|regex:/^[A-Z][A-Za-z0-9]+$/';
    const TIMEZONED_DATE = 'nullable|date_format:Y-m-d\TH:i:sP|after_or_equal:1000-01-01T00:00:00-12:00|before_or_equal:9999-12-31T23:59:59-12:00';

    protected $entity;
    
    /**
     * Create a new job instance.
     *
     * @Param Object $nombreApellido
     * @return void
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $medium = null;
        if (isset($this->entity['medium'])) {
            $medium = $this->entity['medium'];
            unset($this->entity['medium']);
        }
        Validator::make($this->entity, $this->entityPlayloadValidation());
        // $payload = $request->only('id', 'model', 'properties', 'view', 'langs', 'parent_entity_id', 'visibility', 'published_at', 'unpublished_at');
        // TODO: filter each entity fields.
        $filteredPayload = $this->entity;
        $modelClassName = Entity::getEntityClassName(Str::singular($this->entity['model'] ?? 'Entity'));
        if (isset($filteredPayload['id']) && $entity = $modelClassName::withTrashed()->find($filteredPayload['id'])) {
            $entity->fill($filteredPayload);
            $entity->save();
        } else {
            $entity = new $modelClassName($filteredPayload);
            $entity->save();
        }
        if (isset($this->entity['contents'])) EntityContent::createFromArray($entity->id, $this->entity['contents']);
        if (isset($this->entity['routes'])) EntityRoute::createFromArray($entity->id, $this->entity['routes'], $entity->model);
        if (isset($this->entity['entities_related'])) EntityRelation::createFromArray($entity->id, $this->entity['entities_related']);
        if (isset($medium)) {
            $url = $medium['url'];
            unset($medium['url']);
            $tags = [];
            if (isset($medium['tag'])) {
                $tags = [$medium['tag']];
                unset($medium['tag']);
            }
            // $previousMedia = Entity::relatedBy('page_20')->get();
            // return $previousMedia;
            // TODO: verify if media already exist and replace
            $medium = [
                "model" => "medium",
                "view" => "medium",
                "entities_related" => [
                    [
                        "called_entity_id" => $entity->id,
                        "kind" => "medium",
                        "tags" => $tags
                    ]
                ]
            ];
            $mediumEntity = new Entity($medium);
            $mediumEntity->save();
            EntityRelation::createFromArray($mediumEntity->id, $medium['entities_related']);
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $image = file_get_contents($url);
                $size = getimagesize($url);
                $extension = image_type_to_extension($size[2]);
                Storage::disk('local')->put("original/{$mediumEntity->id}{$extension}", $image);
            }
            $mediumEntity->touch();
        }
        if (isset($filteredPayload['id'])) {
            $entity->touch();
            // $updatedEntity = $modelClassName::withContents()->withRoutes()->with('entities_related')->find($entity->id);
        } else {
            $createdEntity = $modelClassName::withContents()->withRoutes()->with('entities_related')->find($entity->id);
            if ($createdEntity && isset($this->entity['relate_to'])) {
                Validator::make($this->entity, $this->entityRelationValidation());
                EntityRelation::create(array_merge($this->entity['relate_to'], ['called_entity_id' => $createdEntity->id]));
            }
            $entity->touch();
        }
    }

    private function entityPlayloadValidation() {
        return [
            'model' => 'string|max:32',
            'view' => 'string|max:32',
            'id' => self::ID_RULE,
            'parent_entity_id' => self::ID_RULE,
            'published_at' => self::TIMEZONED_DATE,
            'unpublished_at' => self::TIMEZONED_DATE,
            'is_active' => 'boolean',
            'contents.*.lang' => 'required_with:contents|string',
            'contents.*.field' => 'required_with:contents|string',
            'contents.*.text' => 'required_with:contents|string',
            'routes.*.path' => 'required_with:routes|string',
            'routes.*.lang' => 'required_with:routes|string',
            'routes.*.kind' => 'required_with:routes|string',
            'relations.*.called_entity_id' => 'required_with:relations|'.self::ID_RULE,
            'relations.*.kind' => 'required_with:relations|string'
        ];
    }
    private function entityRelationValidation() {
        return [
            'relate_to.caller_entity_id' => self::ENTITY_EXISTS,
            'relate_to.kind' => 'required',
            'relate_to.position' => 'integer',
            'relate_to.depth' => 'integer',
            'relate_to.tags.*' => 'string'
        ];
    }
}
