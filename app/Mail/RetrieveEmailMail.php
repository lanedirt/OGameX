<?php

namespace OGame\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RetrieveEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $maskedEmail,
        public readonly string $username,
        public readonly string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('t_external.mail.retrieve_email.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'outgame.mail.retrieve-email',
        );
    }
}
