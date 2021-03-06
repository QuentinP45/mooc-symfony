<?php

namespace OC\PlatformBundle\Twig;

use OC\PlatformBundle\Antispam\OCAntispam;

class AntispamExtension extends \Twig_Extension
{
    private $ocAntispam;

    public function __construct(OCAntispam $ocAntispam)
    {
        $this->ocAntispam = $ocAntispam;
    }

    public function checkIfArgumentIsSpam($text)
    {
        return $this->ocAntispam->isSpam($text);
    }

    public function getFunction()
    {
        return 
        [
            new \Twig_SimpleFunction('checkIfSpam', 
            [
                $this, 'checkIfArgumentIsSpam'
            ]),
        ];
    }

    public function getName()
    {
        return 'OCAntispam';
    }
}