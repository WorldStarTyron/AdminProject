<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Mail met reset code
class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetCode;
    public $email;

    public function __construct($resetCode, $email)
    {
        $this->resetCode = $resetCode;
        $this->email = $email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'ForgetPassword.Email-template',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
