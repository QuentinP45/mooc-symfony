<?php

namespace OC\PlatformBundle\Purge;

class AdvertCleaner 
{
    private $em;

    public function __contruct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function purge($days)
    {
        // Récupérer les annonces dont la date 
        // de modification est plus vieille que $days

        // Vérifier que l'attribut qui contient une 
        // collection soit vide : attribut IS empty
        
        // Supprimer les annonces qui correspondent 
        // aux deux derniers critères
    }
}