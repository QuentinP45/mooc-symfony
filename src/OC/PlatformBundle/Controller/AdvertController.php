<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdvertController extends Controller
{
    public function indexAction()
    {
        return $this->render('@OCPlatform/Advert/index.html.twig', [
            'title' => 'Hello world !',
        ]);
    }

    public function viewAction($id)
    {
        return $this->render('@OCPlatform/Advert/view.html.twig', [
            'id' => $id,
        ]);
    }

    public function addAction(Request $request)
    {
        $session = $request->getSession();

        $session->getFlashBag()->add('info', 'Annonce bien enregistrée');
        $session->getFlashBag()->add('info', 'Oui oui, elle est bien enregistrée !');
        
        return $this->redirectToRoute('oc_platform_view', ['id' => 5]);
    }
}