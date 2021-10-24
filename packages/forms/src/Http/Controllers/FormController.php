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
        $entity = Entity::withContent()->findOrFail($request['entity_id']);
        $allowedFields =array_keys($entity->properties['form']['fields']);
        $toEmail = $entity->properties['form']['mail_to'] ?? Config::get('mail.from.address');
        $payload = $request->only($allowedFields);

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

        return "Form received! ".json_encode($payload);
    }
}
