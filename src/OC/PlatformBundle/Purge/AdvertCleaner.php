<?php

namespace OC\PlatformBundle\Purge;

use OC\PlatformBundle\Entity\Advert;

class AdvertCleaner 
{
    private $em;
    private $currentDate;

    public function __construct($em)
    {
        $this->em = $em; 
        $this->currentDate = new \DateTime();
    }

    public function getPurgeDateLimit($days)
    {
        $currentDate = $this->currentDate;
        $interval = 'P' . $days . 'D';
        return $dateLimit = $currentDate->sub(new \DateInterval($interval));
    }

    public function purge($days)
    {
        $dateLimit = $this->getPurgeDateLimit($days);

        $em = $this->em;

        // Récupérer les annonces dont la date 
        // de modification est plus vieille que $days

        $repository = $em->getRepository(Advert::class);

        $listAdverts = $repository->getAdvertsToPurge($dateLimit);

        // Vérifier que l'attribut qui contient une 
        // collection soit vide : attribut IS empty
        
        // Supprimer les annonces qui correspondent 
        // aux deux derniers critères
    }
}