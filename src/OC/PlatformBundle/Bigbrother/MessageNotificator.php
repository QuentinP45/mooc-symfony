<?php

namespace OC\PlatformBundle\Bigbrother;

use FOS\UserBundle\Model\User as BaseUser;

class MessageNotficator
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notifyByEmail($message, BaseUser $user)
    {
        $message = \Swif_Message::newInstance()
            ->setSubject("Nouveau message d'un utilisateur surveillé")
            ->setFrom('testsmtp180@gmail.com')
            ->setTo('testsmtp180@gmail.com')
            ->setBody("L'utilisateur surveillé \"$user->getUsername()\" a posté le message suivant : $message")
        ;

        $this->mailer->send($message);
    }
}