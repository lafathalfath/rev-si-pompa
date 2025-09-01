<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $payload;
    
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function build() {
        return $this->subject($this->payload['subject'])
                ->view('mails.notification_mail', ['data' => $this->payload]);
    }

}
