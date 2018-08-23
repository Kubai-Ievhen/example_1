<?php

namespace App\Mail;

use App\Resources\SystemParametersSingleton;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Base\HashKey;
use Illuminate\Support\Facades\Auth;

class SupplyAnsverMailing extends Mailable
{
    use Queueable, SerializesModels;
    public $hash_key = '';

    public $event;
    public $data_to_mail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($hash_name, $event, $data_to_mail)
    {
        $this->hash_key = HashKey::keyGenerate(Auth::id(), $hash_name);
        $this->event = $event;
        $this->data_to_mail = $data_to_mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(SystemParametersSingleton::getParam('mail_sender'), env('APP_NAME'))
            ->subject('Confirm Supply')
            ->view('emails.volunteer_answer');
    }
}
