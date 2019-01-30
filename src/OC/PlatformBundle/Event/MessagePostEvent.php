<?php

namespace OC\PlatformBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use FOS\UserBundle\Model\User as BaseUser;

class MessagePostEvent extends Event
{
    protected $message;
    protected $user;

    public function __construct($message, BaseUser $user)
    {
        $this->message = $message;
        $this->user = $user;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        return $this->message = $message;
    }

    public function getUser()
    {
        return $this->user;
    }
}
