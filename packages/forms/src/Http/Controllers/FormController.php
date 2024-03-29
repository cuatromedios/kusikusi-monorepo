<?php

namespace Kusikusi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Kusikusi\Models\Entity;
use Kusikusi\Models\FormEntry;
use Kusikusi\Mail\FormEntryReceived;
use Illuminate\Support\Facades\Mail;

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
        $entity = Entity::withContent()->withRoute()->findOrFail($request['entity_id']);
        $allowedFields =array_keys($entity->properties['form']['fields']);
        $payload = $request->only($allowedFields);
        $request->session()->flash('validated', false);
        $request->validate($entity->properties['form']['fields']);
        $request->session()->flash('validated', true);
        // Save the entry
        $entry = new FormEntry([
            'payload' => $payload,
            'entity_id' => $entity->id,
            'status' => FormEntry::STATUS_UNREAD,
        ]);
        $entry->save();

        // Send an email
        $toEmail = $entity->properties['form']['mail_to'] ?? false;
        if ($toEmail) {
            Mail::to($toEmail)
                ->send(new FormEntryReceived($entry, $entity));
        }
        return redirect($entity->route->path);
    }
}
