<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{
    Address,
    Content,
    Envelope,
};
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailTemplate extends Mailable
{
    use Queueable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * Represents the data for the email.
     *
     * @var mixed $mailData
     */
    public mixed $mailData;

    /**
     * Create a new message instance.
     *
     * @param object $mailData An object containing email information.
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->mailData->sender, config('mail.default_email_name')),
            subject: $this->mailData->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->mailData->template,
            with: ['body' => $this->mailData->body],
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
