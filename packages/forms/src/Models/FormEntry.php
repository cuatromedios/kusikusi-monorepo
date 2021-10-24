<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Model;
use Kusikusi\Casts\Json;

class FormEntry extends Model
{
    public const STATUS_UNREAD = 'unread';

    protected $fillable = ["entry", "entity_id", "status"];
    /**
     * @var array A list of columns from the entities tables and other joins needs to be casted
     */
    protected $casts = [
        'entry' => Json::class
    ];

    /**
     * Get the entities this entity is calling
     */
    public function entitiy()
    {
        return $this->belongsTo('Kusikusi\Models\Entity');
    }
}
