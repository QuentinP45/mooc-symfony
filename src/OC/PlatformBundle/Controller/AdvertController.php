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

        return $this->render('@OCPlatform/Advert/edit.html.twig');
    }

    public function deleteAction($id)
    {
        return $this->render('@OCPlatform/Advert/delete.html.twig');
    }
}