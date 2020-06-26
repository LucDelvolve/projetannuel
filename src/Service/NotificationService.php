<?php

namespace App\Service;

use App\Entity\Contact;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class NotificationService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notify(Contact $contact)
    {
        $email = (new TemplatedEmail(/*'Prestataire :' .$contact->getAd()->getAuthor()->getFullName()*/) )
            ->from('papadiaki90@gmail.com')
            ->to('mehdi-etude@hotmail.com'/*$contact->getAd()->getAuthor()->getEmail()*/)
            ->subject('Time for Symfony Mailer!')
            ->replyTo($contact->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->htmlTemplate('emails/contact.html.twig')
            ->context([
                'contact' => $contact,
            ]);

        $sentEmail = $this->mailer->send($email);
    }
}