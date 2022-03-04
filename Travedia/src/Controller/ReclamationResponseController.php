<?php

namespace App\Controller;

use App\Entity\ReclamationReponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationResponseController extends AbstractController
{
    /**
     * @Route("/reclamation/response", name="reclamation_response")
     */
    public function index(): Response
    {
        return $this->render('reclamation_response/index.html.twig', [
            'controller_name' => 'ReclamationResponseController',
        ]);
    }

    /**
     * @Route("admin/reclamation/supprimer/{id}", name="reclamationSupprimerBack")
     */
    //public function SupprimerReclamationBack($id):Response
    //{

       // $rep =$this->getDoctrine()->getRepository(ReclamationReponse::class);
        //$reclamation = $rep->find($id);
        //$entityManager=$this->getDoctrine()->getManager();
        //$entityManager->remove($reclamation);
        //$entityManager->flush();
        //return $this->redirectToRoute("reclamation_Liste_back");
   // }
}
