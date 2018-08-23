<?php

namespace App\Mail;

use App\Models\Base\HashKey;
use App\Resources\SystemParametersSingleton;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class VolunteerAnsverMailing extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var string
     */
    public $hash_key = '';

    public $event;
    public $volunteer_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($hash_name, $event, $volunteer_name)
    {
        $this->hash_key = HashKey::keyGenerate(Auth::id(), $hash_name);
        $this->event = $event;
        $this->volunteer_name = $volunteer_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(SystemParametersSingleton::getParam('mail_sender'), env('APP_NAME'))
            ->subject('Confirm Volunteer')
            ->view('emails.volunteer_answer');
    }
}
