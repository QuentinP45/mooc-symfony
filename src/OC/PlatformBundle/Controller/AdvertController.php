<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Category;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page ' . $page . ' inexistante.');
        }

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Advert::class);

        $listAdverts = $repository->findAll();


        return $this->render('@OCPlatform/Advert/index.html.twig', [
            'listAdverts' => $listAdverts,
        ]);
    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository(Advert::class)->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id: \"$id\" n'existe pas");
        }

        $listApplications = $advert->getApplications();

        $listAdvertSkills = $em
            ->getRepository(AdvertSkill::class)
            ->getListAdvertSkills($id);

        return $this->render('@OCPlatform/Advert/view.html.twig', [
            'advert' => $advert,
            'listApplication' => $listApplications,
            'listAdvertSkills' => $listAdvertSkills,
        ]);
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

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

        $listSkills = $em->getRepository(Skill::class)->findAll();

        foreach ($listSkills as $skill) {
            $advertSkill = new AdvertSkill();

            $advertSkill->setSkill($skill);
            $advertSkill->setAdvert($advert);
            $advertSkill->setLevel('Expert');

            $em->persist($advertSkill);
        }

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

        $advert->setTitle('Nouveau titre');

        $listCategories = $em->getRepository(Category::class)->findAll();

        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }

        $em->flush();

        if ($request->isMethod('POST')) {
            $session = $request->getSession();
            $session->getFlashBag()->add('notice', 'Annonce bien modifiée.');
            
            return $this->redirectToRoute('oc_platform_view', ['id' => $id]);
        }

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
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Advert::class);

        $listAdverts = $repository->getLastAdverts(3);

        return $this->render('@OCPlatform/Advert/menu.html.twig', [
        'listAdverts' => $listAdverts,
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