<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;

class MailerService
{
    private PHPMailer $phpMailer;
    
    public function __construct(PHPMailer $phpMailer)
    {
        $this->phpMailer = $phpMailer;
    }

    public function sendMessage(
        string $email,
        string $header,
        string $text
    )
    {
        $this->phpMailer->addAddress($email);
        $this->phpMailer->Subject = $header;
        $this->phpMailer->Body    = $text;
        $this->phpMailer->send();
    }
}