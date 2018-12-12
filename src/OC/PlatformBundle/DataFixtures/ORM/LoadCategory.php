<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;

/* 
* Lien pour faire fonctionner lea commande "doctrine:fixtures:load
* https://stackoverflow.com/questions/47613979/symfony-3-4-0-could-not-find-any-fixture-services-to-load
 */

class LoadCategory implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $names = [
            'Développment web',
            'Développement mobile',
            'Graphisme',
            'Intégration',
            'Réseau'
        ];

        foreach ($names as $name) {
            $category = new Category();
            $category->setName($name);

            $manager->persist($category);
        }

        $manager->flush();
    }
}