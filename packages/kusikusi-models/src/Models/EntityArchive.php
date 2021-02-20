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

    public static function archive(string $entity_id) {
        $entityToArchive = Entity::with('contents')
            ->with('routes')
            ->with('entities_related')
            ->find($entity_id);
        EntityArchive::create([
            "entity_id" => $entity_id,
            "kind" => "version",
            "version" => $entityToArchive->version,
            "payload" => $entityToArchive
        ]);
    }
}
