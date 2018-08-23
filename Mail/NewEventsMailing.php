<?php

namespace App\Mail;

use App\Resources\SystemParametersSingleton;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewEventsMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $events;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($events)
    {
        $this->events = $events;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(SystemParametersSingleton::getParam('mail_sender'), env('APP_NAME'))
            ->subject('News Letter')
            ->view('emails.news_letter');
    }
}
