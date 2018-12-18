<?php

namespace OC\PlatformBundle\Purge;

use OC\PlatformBundle\Entity\Advert;

class AdvertCleaner 
{
    private $em;
    private $currentDate;
    private $countPurgedAdverts = 0;

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
        
        foreach( $listAdverts as $advert ) {
            
            // Vérifier que l'attribut qui contient une 
            // collection soit vide : attribut IS EMPTY
            
            $advert->getApplications();

            if ( $advert->getApplications()->isEmpty() ) {

                // Supprimer les annonces qui correspondent 
                // aux deux derniers critères

                $em->remove($advert);

                $this->countPurgedAdverts++;
            }

            $em->flush();
        }
    }

    public function getCountPurgedAdverts()
    {
        return $this->countPurgedAdverts;
    }
}