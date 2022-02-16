<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
class CategorieFController extends AbstractController
{
    /**
     * @Route("/categorie_f", name="categorief")
     */
    public function index(): Response
    {
        return $this->render('categorie_f/index.html.twig', [
            'controller_name' => 'CategorieFController',
        ]);
    }

    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/categorie_f/add", name="categorief_add")
     */
    public function addCat(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
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
                $categorie->setPicture($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();
            return $this->redirectToRoute('categorief_show');
        }

        return $this->render('categorie_f/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param CategorieRepository $rep
     * @return Response
     * @Route("/categorie_f/show", name="categorief_show")
     */
    public function affcat(CategorieRepository $rep)
    {
        $categorie=$rep->findAll();
        return $this->render('categorie_f/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /**
     * @param $id
     * @param CategorieRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/categorie_f/update/{id}", name="categorief_edit")
     */
    public function edit($id,CategorieRepository $rep,Request $request)
    {
        $categorie=$rep->find($id);
        $form = $this->createForm(CategorieType::class, $categorie);
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
                $categorie->setPicture($newFilename);
            }
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('categorief_show');
        }

        return $this->render('categorie_f/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param CategorieRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/categorie_f/delete/{id}", name="categorief_delete")
     */
    public function suppcat($id,CategorieRepository $rep)
    {
        $categorie = $rep->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();


        return $this->redirectToRoute('categorief_show');
    }
}
