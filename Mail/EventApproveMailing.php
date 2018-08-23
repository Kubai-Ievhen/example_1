<?php

namespace App\Mail;

use App\Resources\SystemParametersSingleton;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventApproveMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $has_many;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($event,$has_many)
    {
        $this->event = $event;
        $this->has_many = $has_many;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(SystemParametersSingleton::getParam('mail_sender'), env('APP_NAME'))
            ->subject('Event is Approved')
            ->view('emails.event_approve');
    }
}
