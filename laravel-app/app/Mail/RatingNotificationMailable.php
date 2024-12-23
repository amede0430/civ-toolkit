<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RatingNotificationMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $data, $sender;

    /**
     * Create a new message instance.
     */
    public function __construct($rating)
    {
        $this->sender = User::where('role', 'admin')->first();
        $this->data = [
            'engineer' => User::find($rating->user_id)->name,
            'plan' => Plan::find($rating->plan_id)->title,
            'rating' => $rating->rating,
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->sender->email, $this->sender->name), 
            subject: 'Attribution de note a l\'un de vos plans',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.rating-notification',
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
