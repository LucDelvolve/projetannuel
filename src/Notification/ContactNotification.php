<?php

namespace App\Notification;

use App\Entity\Contact;
use Twig\Environment;

//npm install -g maildev # Utilisez sudo si nécessaire
//maildev pour demarrer 
// MAILER_URL=smtp://localhost:1025 changer dans le .env pour maildev

class ContactNotification{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer){

        $this->mailer = $mailer;
        $this->renderer = $renderer;

    }

    public function notify(Contact $contact){
        $message = ( new \Swift_Message('Prestataire :' .$contact->getAd()->getAuthor()->getFullName()))
            ->setFrom('noreply@prestataire.fr')
            ->setTo($contact->getPrestataire()->getEmail())
            ->setReplyTo($contact->getEmail())
            ->setBody($this->renderer->render('emails/contact.html.twig',[
                'contact' => $contact
            ]), 'text/html');
        $this->mailer->send($message);
    }

}