<?php

namespace App\Mail;

use App\Models\Base\HashKey;
use App\Resources\SystemParametersSingleton;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class RegistrationMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $hash_key = '';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($hash_name)
    {
        $this->hash_key = HashKey::keyGenerate(Auth::id(), $hash_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(SystemParametersSingleton::getParam('mail_sender'), env('APP_NAME'))
            ->subject('Confirm Registration')
            ->view('emails.registration');
    }
}
