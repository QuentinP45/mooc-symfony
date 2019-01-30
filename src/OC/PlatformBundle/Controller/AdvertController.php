<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Category;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Event\PlatformEvents;
use OC\PlatformBundle\Event\MessagePostEvent;

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
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTHOR')) {
            throw new AccessDeniedException('Accès limité aux auteurs');
        }

        $advert = new Advert();

        $form = $this->get('form.factory')->create(AdvertType::class, $advert);

        if ($request->isMethod('POST')) {
            
            $form->handleRequest($request);

            if ($form->isValid()) {

                $user = $this->getUser();
                $advert->setUser($user);

                // create event with 2 arguments
                $event = new MessagePostEvent($advert->getContent(), $advert->getUser());

                // trigger event
                $this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);

                // got result from listener
                $advert->setContent($event->getMessage());

                $em = $this->getDoctrine()->getManager();

                $advert->setIp($request->getClientIp());

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

        $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);


        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $em->flush();

                $session = $request->getSession();
                $session->getFlashBag()->add('notice', 'Annonce bien modifiée.');
                
                return $this->redirectToRoute('oc_platform_view', ['id' => $id]);
            }
        }

        return $this->render('@OCPlatform/Advert/edit.html.twig', [
            'form' => $form->createView(),
            'advert' => $advert,
        ]);
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        
        $advert = $em->getrepository(Advert::class)->find($id);
        
        
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id : $id n'existe pas.");
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('token');
            
            if ($this->isCsrfTokenValid('token-csrf', $submittedToken)) {
                $em->remove($advert);
                $em->flush();

                $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

                return $this->redirectToRoute('oc_platform_home');
            }
        }

        return $this->render('@OCPlatform/Advert/delete.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
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