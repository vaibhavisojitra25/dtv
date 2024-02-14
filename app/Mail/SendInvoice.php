<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $flag;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data,$flag)
    {
        $this->data = $data;
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
            return $this->view('/emails/send_invoice')->subject('Your Payment of '.env('CURRENCY').''. $this->data['plan_amount'].' Received');
        }else{
            return $this->view('/emails/send_invoice')->subject('Your Invoice Details - '.\Carbon\Carbon::parse($this->data['response']['created_at'])->setTimezone('Asia/Kolkata')->format('M d, Y h:i a'));
        }
    }
}
