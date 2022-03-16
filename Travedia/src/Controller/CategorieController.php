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
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Knp\Component\Pager\PaginatorInterface;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie", name="categorie")
     */
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }

    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/categorie/add", name="categorie_add")
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
            return $this->redirectToRoute('categorie_show');
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param CategorieRepository $rep
     * @return Response
     * @Route("/categorie/show", name="categorie_show")
     */
    public function affcat(CategorieRepository $rep, Request $request,PaginatorInterface $paginator)
    {
        $categorie=$rep->findAll();
        $categorie = $paginator->paginate(
            $categorie,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 3)/*limit per page*/
        );
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /**
     * @param $id
     * @param CategorieRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/categorie/update/{id}", name="categorie_edit")
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

            return $this->redirectToRoute('categorie_show');
        }

        return $this->render('categorie/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param CategorieRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/categorie/delete/{id}", name="categorie_delete")
     */
    public function suppcat($id,CategorieRepository $rep)
    {
        $categorie=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();


        return $this->redirectToRoute('categorie_show');
    }


}
