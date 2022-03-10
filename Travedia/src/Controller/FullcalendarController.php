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
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\File;

class FullcalendarController extends AbstractController
{
    /**
     * @Route("/fullcalendar", name="fullcalendar")
     */
    public function index(EvenementRepository $evenement): Response
    {
        $events = $evenement->findAll();

        $rdvs = [];

        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'title' => $event->getNom(),
                'description' => $event->getDescription(),
                'start' => $event->getDatedeb()->format('Y-m-d'),
                'end' => $event->getDatefin()->format('Y-m-d'),
                'categorie' => $event->getCategorie(),
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('fullcalendar/index.html.twig', compact('data'));
    }
    /**
     * @Route("/fullcalendar/{id}/edit", name="fullcalendar_edit", methods={"PUT"})
     */
    public function editCalendar(Evenement $event, Request $request )
    {
        $donnees = json_decode($request->getContent());

            $code= 200;
        $event->setDatefin(new \DateTime($donnees->End));

        $event->setDatedeb(new \DateTime($donnees->Start));
            $em= $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            return new Response('OK', $code);


    }
}
