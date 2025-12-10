<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExitInterviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formLink;
    public $recipientEmail;

    /**
     * Create a new message instance.
     */
    public function __construct($formLink, $recipientEmail)
    {
        $this->formLink = $formLink;
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Exit Interview Form - 5 Core',
            from: env('MAIL_FROM_ADDRESS', 'admin@new.5coremanagement.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.exit-interview',
            with: [
                'formLink' => $this->formLink,
                'recipientEmail' => $this->recipientEmail,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
