<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AcceptPlanMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Crée une nouvelle instance de message.
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Définit l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
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
