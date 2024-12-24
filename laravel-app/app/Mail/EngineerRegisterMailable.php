<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class EngineerRegisterMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $userData, $sender;
    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->userData = $user;
        $this->sender = User::where('role', 'admin')->first();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->sender->email, $this->sender->name),
            subject: 'Enregistrement d\'un nouvel inénieur',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.engineer-register',
            with: ['data' => $this->userData],
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
