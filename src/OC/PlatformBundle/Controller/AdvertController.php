<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Category;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page ' . $page . ' inexistante.');
        }

        return $this->render('@OCPlatform/Advert/index.html.twig', [
            'listAdverts' => [
                [
                    'title'   => 'Recherche développpeur Symfony',
                    'id'      => 1,
                    'author'  => 'Alexandre',
                    'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',      
                    'date'    => new \Datetime()
                ],

                [
                    'title'   => 'Mission de webmaster',
                    'id'      => 2,
                    'author'  => 'Hugo',
                    'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                    'date'    => new \Datetime()
                ],

                [
                    'title'   => 'Offre de stage webdesigner',
                    'id'      => 3,
                    'author'  => 'Mathieu',
                    'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                    'date'    => new \Datetime()
                ],
            ]
        ]);
    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository(Advert::class)->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id: \"$id\" n'existe pas");
        }

        $listApplications = $em
            ->getRepository(Application::class)
            ->findBy(['advert' => $advert]);

        return $this->render('@OCPlatform/Advert/view.html.twig', [
            'advert' => $advert,
            'listApplication' => $listApplications,
        ]);
    }

    public function addAction(Request $request)
    {
        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Justine');
        $advert->setContent('Nous recherchons un développeur Symfony débutant sur Orléans. blablabla...');

        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        $advert->setImage($image);

        $application1 = new Application();
        $application1->setAuthor('Marine');
        $application1->setContent('J\'ai toutes les qualités requises');
        
        $application2 = new Application();
        $application2->setAuthor('Pierre');
        $application2->setContent('Je suis très motivé');

        $application1->setAdvert($advert);
        $application2->setAdvert($advert);

        $em = $this->getDoctrine()->getManager();
        $em->persist($advert);
        $em->persist($application1);
        $em->persist($application2);
        $em->flush();

        if ($request->isMethod('POST')) {
            $session = $request->getSession();
            $session->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            
            return $this->redirectToRoute('oc_platform_view', ['id' => $advert->getId()]);
        }
        return $this->render('@OCPlatform/Advert/add.html.twig');
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository(Advert::class)->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id : $id n'existe pas.");
        }

        $listCategories = $em->getRepository(Category::class)->findAll();

        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }

        $em->flush();

        if ($request->isMethod('POST')) {
            $session = $request->getSession();
            $session->getFlashBag()->add('notice', 'Annonce bien modifiée.');
            
            return $this->redirectToRoute('oc_platform_view', ['id' => 5]);
        }

        $advert = [
            'title'   => 'Recherche développpeur Symfony',
            'id'      => $id,
            'author'  => 'Alexandre',
            'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
            'date'    => new \Datetime()
        ];

        return $this->render('@OCPlatform/Advert/edit.html.twig', [
            'advert' => $advert
        ]);
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getrepository(Advert::class)->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id : $id n'existe pas.");
        }

        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        $em->flush();

        return $this->render('@OCPlatform/Advert/delete.html.twig');
    }

    public function menuAction($limit)
    {
        // On fixe en dur une liste ici, bien entendu par la suite
        // on la récupérera depuis la BDD !

        $listAdverts = [
            ['id' => 2, 'title' => 'Recherche développeur Symfony'],
            ['id' => 5, 'title' => 'Mission de webmaster'],
            ['id' => 9, 'title' => 'Offre de stage webdesigner'],
        ];
  
        return $this->render('@OCPlatform/Advert/menu.html.twig', [
  
        // Tout l'intérêt est ici : le contrôleur passe
        // les variables nécessaires au template !
  
        'listAdverts' => $listAdverts
  
        ]);
    }

    public function editImageAction($advertId)
    {
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Advert::class);
        
        $advert = $repository->find($advertId);

        $advert->getImage()->setUrl('kaboom');

        $em->flush();
    }
}