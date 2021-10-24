<?php

namespace Kusikusi\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormEntryReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($entry, $entity)
    {
        $this->entry = $entry;
        $this->entity = $entity;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("{$this->entity->content['title']} ({$this->entry->id})")
            ->view('kusikusi_forms::mail.form_entry', [
                "entry" => $this->entry,
                "entity" => $this->entity,
                "payload" => $this->entry->payload
            ]);
    }
}
