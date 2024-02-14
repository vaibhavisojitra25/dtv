<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $forgotPassword;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $forgotPassword)
    {
        $this->user = $user;
        $this->forgotPassword = $forgotPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.forgot_password');
    }
}
