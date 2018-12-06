<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $advert = [
            'title'   => 'Recherche développpeur Symfony2',
            'id'      => $id,     
            'author'  => 'Alexandre',     
            'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',     
            'date'    => new \Datetime()     
        ];

        return $this->render('@OCPlatform/Advert/view.html.twig', [
            'advert' => $advert,
        ]);
    }

    public function addAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $session = $request->getSession();
            $session->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            
            return $this->redirectToRoute('oc_platform_view', ['id' => 5]);
        }
        return $this->render('@OCPlatform/Advert/add.html.twig');
    }

    public function editAction($id, Request $request)
    {
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
}