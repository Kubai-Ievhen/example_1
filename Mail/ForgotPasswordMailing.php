<?php

namespace App\Mail;

use App\Models\Base\HashKey;
use App\Resources\SystemParametersSingleton;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordMailing extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var string
     */
    public $hash_key = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($hash_name, $user_id)
    {
        $this->hash_key = HashKey::keyGenerate($user_id, $hash_name);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(SystemParametersSingleton::getParam('mail_sender'), env('APP_NAME'))
            ->subject('Change Password')
            ->view('emails.forgot_password');
    }
}
