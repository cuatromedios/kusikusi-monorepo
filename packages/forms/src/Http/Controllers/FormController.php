<?php

namespace Kusikusi\Http\Controllers;

use Illuminate\Http\Request;
use Kusikusi\Models\Entity;
use Kusikusi\Models\FormEntry;

class FormController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function receive(Request $request) {
        $entity = Entity::findOrFail($request['entity_id']);
        $allowedFields =array_keys($entity->properties['form']['fields']);
        $payload = $request->only($allowedFields);
        $entry = new FormEntry([
            'entry' => $payload,
            'entity_id' => $entity->id,
            'status' => FormEntry::STATUS_UNREAD,
        ]);
        $entry->save();
        return "Form received! ".json_encode($payload);
    }
}
