<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Evenement;
use App\Form\CategorieType;
use App\Form\EvenementType;
use App\Repository\CatRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;


class EvenementController extends AbstractController
{
    /**
     * @Route("/evenement", name="evenement")
     */
    public function index(): Response
    {
        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
        ]);
    }

    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/evenement/add", name="evenement_add")
     */
    public function addEv(Request $request): Response
    {
        $evenment = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenment);
        $form->handleRequest($request);
//winou code limage houni
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
            return $this->redirectToRoute('evenement_show');
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param EvenementRepository $rep
     * @return Response
     * @Route("/evenement/show", name="evenement_show")
     */
    public function affcat(EvenementRepository $rep)
    {
        $evenement=$rep->findAll();
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    /**
     * @param $id
     * @param EvenementRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/evenement/update/{id}", name="evenement_edit")
     */
    public function edit($id,EvenementRepository $rep,Request $request)
    {
        $evenement=$rep->find($id);
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->remove('picture');
        $form->add('picture', FileType::class, [
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'image/*',
                    ],
                    'mimeTypesMessage' => 'Verify your image type',
                ])
            ],
        ]);

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

            return $this->redirectToRoute('evenement_show');
        }

        return $this->render('evenement/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param EvenementRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/evenement/delete/{id}", name="evenement_delete")
     */
    public function suppcat($id,EvenementRepository $rep)
    {
        $evenement=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($evenement);
        $entityManager->flush();


        return $this->redirectToRoute('evenement_show');
    }
}
