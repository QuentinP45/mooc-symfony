<?php

namespace OC\PlatformBundle\Email;

class ApplicationMailer
{
    private $mailer;

    public function __construct(\Swuft_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendNewNotification(Application $application)
    {
        $message = new \Swift_Message(
            'Nouvelle candidature',
            'VOus avez reçu une nouvelle candidature.'
        );

        $message
            ->addTo($application->getAdvert()->getAuthor())
            ->addFrom('tyijp@yopmail.com')
        ;

        $this->mailer->send($message);
    }
}