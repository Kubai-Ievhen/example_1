<?php

namespace App\Mail;

use App\Resources\SystemParametersSingleton;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VolunteerReminderMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $response;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(SystemParametersSingleton::getParam('mail_sender'), env('APP_NAME'))
            ->subject('Volunteer Reminder')
            ->view('emails.volunteer_reminder');
    }
}
