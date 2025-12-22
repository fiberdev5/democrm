<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenantName;
    public $tenantEmail;
    public $companyName;

    public function __construct($tenantName, $tenantEmail, $companyName)
    {
        $this->tenantName = $tenantName;
        $this->tenantEmail = $tenantEmail;
        $this->companyName = $companyName;
    }

    public function build()
    {
        return $this->subject('Serbis - Kayıt Başarılı')
                    ->view('emails.registration_success');
    }
}