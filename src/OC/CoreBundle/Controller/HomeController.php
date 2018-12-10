<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function indexAction(Request $request)
    {
        if ($request->query->get('tag') === 'contact') {
            $session = $request->getSession()->getFlashBag()->add('info', 'Page de contact en construction, merci de revenir plus tard !');
        }

        return $this->render('@OCCore/Home/index.html.twig');
    }
}
