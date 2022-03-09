<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Evenement;
use App\Form\CategorieType;
use App\Form\EvenementType;
use App\Repository\CatRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Knp\Component\Pager\PaginatorInterface;

class EvenementFController extends AbstractController
{
    /**
     * @Route("/evenement_f", name="evenementf")
     */
    public function index(EvenementRepository $rep): Response
    {
        $evenement=$rep->findAll();
        return $this->render('evenement_f/index.html.twig', [
            'evenement' => $evenement,
        ]);
    }
    /**
     * @Route("/evenement_f/{id}/editRating/{rating}", name="evenement_rating")
     */
    public function editRating(Request $request, Evenement $evenement, int $rating): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $evenement->setRating($rating);
        $entityManager->persist($evenement);
        $entityManager->flush();

        return new Response("1");
    }
    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/evenement_f/add", name="evenementf_add")
     */
    public function addEv(Request $request): Response
    {
        $evenment = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->get('picture')->getData();

            if ($picture) {
                $newFilename = uniqid().'.'.$picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('event_picture'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $evenment->setPicture($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenment);
            $entityManager->flush();
            return $this->redirectToRoute('evenementf');
        }

        return $this->render('evenement_f/new.html.twig', [
            'evenement' => $evenment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param EvenementRepository $rep
     * @return Response
     * @Route("/evenement_f/show/{id}", name="evenementf_show")
     */
    public function affcat(EvenementRepository $rep, $id)
    {
        /*$evenement=$rep->findAll();
        return $this->render('evenement_f/show.html.twig', [
            'evenement' => $evenement,
        ]);*/
        $evenement=$rep->findAll();
        return $this->render('evenement_f/show.html.twig', [
            'evenement' => $evenement,
            'event' => $rep->getEventsByCategoryID($id),
        ]);

    }

    /**
     * @param $id
     * @param EvenementRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/evenement_f/update/{id}", name="evenementf_edit")
     */
    public function edit($id,EvenementRepository $rep,Request $request)
    {
        $evenement=$rep->find($id);
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->get('picture')->getData();
            if ($picture) {
                $newFilename = uniqid().'.'.$picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('event_picture'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $evenement->setPicture($newFilename);
            }
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('evenementf_show');
        }

        return $this->render('evenement_f/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param EvenementRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/evenement_f/delete/{id}", name="evenementf_delete")
     */
    public function suppcat($id,EvenementRepository $rep)
    {
        $evenement=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($evenement);
        $entityManager->flush();


        return $this->redirectToRoute('evenementf');
    }
}
