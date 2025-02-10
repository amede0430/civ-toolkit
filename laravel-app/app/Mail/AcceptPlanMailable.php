<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Mail\Mailables\Address;

class AcceptPlanMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $mailData, $sender;

    /**
     * Crée une nouvelle instance de message.
     */
    public function __construct($mailData)
    {
        $this->sender = User::where('role', 'admin')->first();
        $this->mailData = $mailData;
    }

    /**
     * Définit l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->sender->email, $this->sender->name),
            subject: 'Décision sur le plan : ' . $this->mailData['plan_title'], 
        );
    }

    /**
     * Définit le contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.accept_plan', 
            with: ['mailData' => $this->mailData], 
        );
    }

    /**
     * Définit les pièces jointes du message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
