<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Destination;
use App\Entity\Evenement;
use App\Entity\Planning;
use App\Form\PlanningType;
use App\Repository\PlanningRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\DestinationRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class PlanningController extends AbstractController
{
    /**
     * @Route("/planning", name="planning")
     */
    public function index(): Response
    {
        return $this->render('baseback.html.twig', [
            'controller_name' => 'PlanningController',
        ]);
    }

    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/planning/add", name="Planning_add")
     */
    public function addPlanning(Request $request): Response
    {


        $planning = new Planning();
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planning);
            $entityManager->flush();
            return $this->redirectToRoute('Planning_show');
        }

        return $this->render('planning/addPlanning.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param PlanningRepository $rep
     * @return Response
     * @Route("/planning/show",name="Planning_show")
     */
    public function showPlanning(PlanningRepository $rep)
    {
        $planning=$rep->findAll();
        return $this->render('planning/showPlanning.html.twig', [
            'planning' => $planning,
        ]);
    }

    /**
     * @param $id
     * @param PlanningRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/planning/editPlanning/{id}",name="Planning_edit")
     */
    public function editPlanning($id,PlanningRepository $rep,Request $request)
    {
        $planning=$rep->find($id);
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('Planning_show');
        }

        return $this->render('planning/editPlanning.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param PlanningRepository $rep
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/planning/delPlanning/{id}",name="Planning_del")
     */
    public function delPlanning($id,PlanningRepository $rep)
    {
        $planning=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($planning);
        $entityManager->flush();


        return $this->redirectToRoute('Planning_show');
    }
    /**
     * @Route("/planningFront", name="planningFront")
     */
    public function indexFront(): Response
    {
        return $this->render('basefront.html.twig', [
            'controller_name' => 'PlanningController',
        ]);
    }

    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/planningFront/add", name="PlanningFront_add")
     */
    public function addPlanningFront(Request $request): Response
    {


        $planning = new Planning();
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planning);
            $entityManager->flush();
            return $this->redirectToRoute('PlanningFront_show');
        }

        return $this->render('planning/addPlanningFront.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @param PlanningRepository $rep
     * @return Response
     * @Route("/planningFront/show",name="PlanningFront_show")
     */
    public function showPlanningFront(PlanningRepository $rep)
    {
        $planning=$rep->findAll();
        return $this->render('planning/showPlanningFront.html.twig', [
            'planning' => $planning,
        ]);
    }
    /**
     * @param $id
     * @param PlanningRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/planning/editPlanningFront/{id}",name="PlanningFront_edit")
     */
    public function editPlanningFront($id,PlanningRepository $rep,Request $request)
    {
        $planning=$rep->find($id);
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('PlanningFront_show');
        }

        return $this->render('planning/editPlanningFront.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @param $id
     * @param PlanningRepository $rep
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/planning/delPlanningFront/{id}",name="PlanningFront_del")
     */
    public function delPlanningFront($id,PlanningRepository $rep)
    {
        $planning=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($planning);
        $entityManager->flush();


        return $this->redirectToRoute('PlanningFront_show');
    }
}