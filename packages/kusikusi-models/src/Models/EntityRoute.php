<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Model;

class EntityRoute extends Model
{

    const KIND_PRIMARY = 'primary';
    const KIND_ALIAS = 'alias';
    const KIND_PERMANENT_REDIRECT = 'permanent_redirect';
    const KIND_TEMPORAL_REDIRECT = 'temporal_redirect';

    protected $table = 'entities_routes';
    protected $fillable = ["entity_id", "path", "lang", "kind"];

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
