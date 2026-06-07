<?php

namespace OGame\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $resetUrl,
        public readonly string $username,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('t_external.mail.reset_password.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'outgame.mail.reset-password',
        );
    }
}
