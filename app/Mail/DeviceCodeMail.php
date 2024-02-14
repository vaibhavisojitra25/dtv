<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeviceCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $device;
    public $deviceCode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$device,$deviceCode)
    {
        $this->user = $user;
        $this->device = $device;
        $this->deviceCode = $deviceCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.device_code')->subject('Device Added to Your Account');

    }
}
