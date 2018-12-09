<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('OCCoreBundle:Home:index.html.twig');
    }
}
