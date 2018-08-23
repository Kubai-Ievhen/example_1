<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUsToAdminMailing extends Mailable
{
    use Queueable, SerializesModels;

    public $message = '';
    public $user_email = '';
    public $user_name = '';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $user_email, $user_name)
    {
        $this->message = $message;
        $this->user_email = $user_email;
        $this->user_name = $user_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->user_email, $this->user_name)
            ->subject('Contact Us')
            ->view('emails.contact_us_admin');
    }
}
