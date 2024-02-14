<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreditAddDeduct extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $credit;
    public $flag;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$credit,$flag)
    {
        $this->user = $user;
        $this->credit = $credit;
        $this->flag = $flag;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->flag == 1){
            return $this->view('emails.credit_add_deduct')->subject('Credit Added to Your Account');
        }else{
            return $this->view('emails.credit_add_deduct')->subject('Credit Deduct to Your Account');
        }
    }
}
