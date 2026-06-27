<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyDigestMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $digest
     */
    public function __construct(
        public User $user,
        public array $digest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Flexter week in review',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.weekly-digest',
        );
    }
}
