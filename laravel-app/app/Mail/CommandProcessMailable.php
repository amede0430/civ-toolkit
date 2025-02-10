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

class CommandProcessMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $mailData, $sender;
    public $tries = 5;
    public $maxExceptions = 3;
    public $timeout = 30;

    /**
     * Create a new message instance.
     */
    public function __construct($processCommand, $command)
    {
        $this->sender = User::where('role', 'admin')->first();
        $this->mailData = $processCommand;
        $this->mailData['command'] = $command;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->sender->email, $this->sender->name),
            subject: 'Reponse à votre demande de plan',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.command-process',
            with: ['mailData' => $this->mailData],
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
