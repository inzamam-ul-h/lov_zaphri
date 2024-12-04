<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\TestEmailSend;
use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $subject = $this->details['subject'];
        
        $custom_id = $this->details['custom_message_id'];
        $log_type = $this->details['custom_log_type'];
        
        $mail_receiver_name = $this->details['mail_receiver_name'];
        $mail_receiver_email = $this->details['mail_receiver_email'];
        
        $mail_sender_name = $this->details['mail_sender_name'];
        $mail_sender_email = $this->details['mail_sender_email'];
        
        $data = $this->details;             
        
        Mail::send('emails.emails', $data, function($message) use ($subject, $custom_id, $log_type, $mail_receiver_name, $mail_receiver_email, $mail_sender_name, $mail_sender_email) 
        {
            $message->to($mail_receiver_email, $mail_receiver_name);
            $message->subject($subject);
            $message->from($mail_sender_email,$mail_sender_name);
            
            $message->getHeaders()->addTextHeader(
                'custom_message_id', $custom_id
            );
            
            $message->getHeaders()->addTextHeader(
                'custom_log_type', $log_type
            );
        });
    }
}
