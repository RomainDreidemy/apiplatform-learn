<?php


namespace App\Service;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(private string $from, private MailerInterface $mailer){}

    public function send(string $to, string $subject, string $content)
    {
        $email = (new Email())
            ->from($this->from)
            ->to($to)
            ->subject($subject)
            ->text($content)
        ;

        $this->mailer->send($email);
    }

}