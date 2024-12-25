<?php

namespace App\Mail;

use App\Models\Categorie;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SoumissionPlanMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $data, $engineer;

    /**
     * Create a new message instance.
     */
    public function __construct($plan)
    {
        $this->engineer = User::find($plan['user_id']);
        $this->data = [
            'username' => $this->engineer->name,
            'category' => Categorie::find($plan['category_id'])->name,
            'title' => $plan['title'],
            'description' => $plan['description'],
            'price' => $plan['price'],
            'free' => $plan['free'] ? 'Oui' : 'Non',
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->engineer->email, $this->engineer->name),
            subject: 'Soumission d\'un plan.',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.soumission-plan',
            with: ['data' => $this->data],
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
