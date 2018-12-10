<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('@OCCore/Home/index.html.twig');
    }

    public function contactAction(Request $request)
    {
        $session = $request->getSession()->getFlashBag()->add('info', 'Page de contact en construction, merci de revenir plus tard !');
        return $this->redirectToRoute('oc_core_homepage');
    }
}
