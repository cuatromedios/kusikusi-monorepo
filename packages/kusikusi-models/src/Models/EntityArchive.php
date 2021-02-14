<?php

namespace Cuatromedios\Kusikusi\Models;

use Illuminate\Database\Eloquent\Model;
use Kusikusi\Models\Traits\UsesShortId;

class EntityArchive extends Model
{
    use UsesShortId;

    protected $table = 'entities_archives';
    protected $fillable = ["entity_id", "kind", "version", "payload"];

    public function entity () {
        return $this->belongsTo('Cuatromedios\Kusikusi\Models\Entity');
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
