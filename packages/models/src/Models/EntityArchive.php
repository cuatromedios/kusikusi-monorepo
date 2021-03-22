<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Model;

class EntityArchive extends Model
{

    protected $table = 'entities_archives';
    protected $fillable = ["entity_id", "kind", "version", "payload"];

    public function entity () {
        return $this->belongsTo('Kusikusi\Models\Entity');

    }

    public static function archive(string $entity_id, string $kind) {
        $entityToArchive = Entity::with('contents')
        ->with('routes')
        ->with('entities_related')
        ->withTrashed()
        ->find($entity_id);
        $archive = EntityArchive::create([
            "entity_id" => $entity_id,
            "kind" => $kind,
            "payload" => $entityToArchive
        ]);
        return $archive;
    }

    public static function updateFromArchive(string $entity_id, $archive_id) {
        $archiveToEntity = EntityArchive::select('payload')
        /* ->where('entity_id', $entity_id) */
        ->where('archive_id', $archive_id)
        ->value('payload');
        $jsonArchive = json_decode($archiveToEntity);
        
        //Setting archive to entity
        Entity::find($entity_id)
        ->update([
            "view" => $jsonArchive->view, 
            "model" => $jsonArchive->model, 
            "properties" => $jsonArchive->properties, 
            "visibility" => $jsonArchive->visibility, 
            "parent_entity_id" => $jsonArchive->parent_entity_id, 
            "created_at" => $jsonArchive->created_at, 
            "created_by" => $jsonArchive->created_by, 
            "deleted_at" => $jsonArchive->deleted_at, 
            "published_at" => $jsonArchive->published_at,  
        ]);
            
        //Deleting entity contents
        EntityContent::select()
        ->where('entity_id', $entity_id)
        ->delete();

        //Setting contents archive to entity contents
        foreach($jsonArchive->contents as $contents){
            EntityContent::create([
                "entity_id" => $entity_id,
                "lang" => $contents->lang,
                "field" => $contents->field,
                "text" => $contents->text,
            ]);
        }

        //Deleting entities relations
        EntityRelation::select()
        ->where('caller_entity_id', $entity_id)
        ->where('kind', '!=', 'ancestor')
        ->delete();

        //Setting entities relations
        foreach($jsonArchive->entities_related as $relation){
            EntityRelation::create([
                "caller_entity_id" => $entity_id,
                "called_entity_id" => $relation->called_entity_id,
                "kind" => $relation->kind,
                "position" => $relation->position,
                "depth" => $relation->depth,
                "tags" => $relation->tags,
            ]);
        }

        $entity = Entity::find($entity_id);
        return $entity;
    }
}
