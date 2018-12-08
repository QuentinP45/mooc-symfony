<?php

namespace OC\PlatformBundle\Antispam;

class OCAntispam 
{
    public function isSpam($text)
    {
        return strlen($text) < 50;
    }
}