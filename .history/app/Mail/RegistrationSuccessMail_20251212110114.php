<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $firmaAdi;
    public $firmaKodu;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $email, $firmaAdi, $firmaKodu)
    {
        $this->name = $name;
        $this->email = $email;
        $this->firmaAdi = $firmaAdi;
        $this->firmaKodu = $firmaKodu;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Serbis - Kayıt Başarılı',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'frontend.secure.emails.registration_success',
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
