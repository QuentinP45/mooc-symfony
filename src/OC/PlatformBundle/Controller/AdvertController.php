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
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdvertController extends Controller
{
    public function purgeAction($days, Request $request)
    {
        $advertCleaner = $this->get('oc_platform.purge.advert_cleaner');

        $advertCleaner->purge($days);

        $purgedAdverts = $advertCleaner->getCountPurgedAdverts();

        if ($purgedAdverts > 0) {
            $session = $request
                ->getSession();

            $session
                ->getFlashBag()
                ->add('info', "$purgedAdverts annonce(s) supprimée(s) de la liste");
        }

        return $this->redirectToRoute('oc_platform_home');
    }

    public function indexAction($page)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page ' . $page . ' inexistante.');
        }

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Advert::class);

        $listAdverts = $repository->getAdverts();

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
        $advert = new Advert();

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $advert);

        $formBuilder
            ->add('date', DateType::class)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('author', TextType::class)
            ->add('published', CheckboxType::class, [
                'required' => false,
            ])
            ->add('save', SubmitType::class)
        ;

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
                return $this->redirectToRoute('oc_platform_view', ['id' => $advert->getId()]);
            }
        }

        return $this->render('@OCPlatform/Advert/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository(Advert::class)->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id : $id n'existe pas.");
        }

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

        $listAdverts = $repository
            ->findBy(
                [],
                ['date' => 'DESC']
                , 
                $limit, 
                0
            );

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