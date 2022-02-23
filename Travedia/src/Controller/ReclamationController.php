<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Utilisateur;
use App\Form\ReclamationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationController extends AbstractController
{
    /**
     * @Route("/reclamation", name="reclamation_Liste")
     */
    public function AfficherReclamationListe(): Response
    {
        $reclamationRep = $this->getDoctrine()->getRepository(Reclamation::class);
        //Special Query for reclamations concerning current user only
        //Instead of findAll, we use FindBy
        return $this->render('reclamation/afficherReclamationListe.html.twig', [
            'reclamations' => $reclamationRep->findAll(),
        ]);
    }

    /**
     * @Route("admin/reclamation", name="reclamation_Liste_back")
     */
    public function AfficherReclamationListeBack(): Response
    {
        $reclamationRep = $this->getDoctrine()->getRepository(Reclamation::class);
        $reclamations =$reclamationRep->findAll();
        return $this->render('reclamation/afficherReclamationListeBack.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    /**
     * @Route("reclamation/ajoutRec", name="reclamation_ajout")
     */
    public function AjouterReclamation(Request $request ): Response
    {
        //PLACEHOLDER FOR UTILISATEUR
        $util = new Utilisateur();
        $utilRep= $this->getDoctrine()->getRepository(Utilisateur::class);
        $util = $utilRep->find(1);
        //INITIALISATION DE L'ETAT EN COURS PAR DEFAULT
        $reclamation = new Reclamation();
        $reclamation->setEtatReclamation("En Cours");
        $reclamation->setUtilisateur($util);
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->add('Envoyer',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('reclamation_Liste');
        }

        return $this->render('reclamation/ajoutReclamation.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/reclamation/ajoutRec", name="reclamation_ajout_back")
     */
    public function AjouterReclamationBack(Request $request ): Response
    {
        $util = new Utilisateur();
        $utilRep= $this->getDoctrine()->getRepository(Utilisateur::class);
        //PLACEHOLDER
        $util = $utilRep->find(1);
        $reclamation = new Reclamation();
        $reclamation->setEtatReclamation("En Cours");
        $reclamation->setUtilisateur($util);
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->add('Envoyer',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('reclamation_Liste_back');
        }

        return $this->render('reclamation/ajoutReclamationBack.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("reclamation/{id}", name="reclamation_afficher")
     */
    public function afficherReclamation(Reclamation $reclamation, $id): Response
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        return $this->render('reclamation/reclamationAfficher.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    /**
     * @Route("admin/reclamation/afficher/{id}", name="reclamation_afficher_back")
     */
    public function afficherReclamationBack( $id): Response
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        return $this->render('reclamation/reclamationAfficherBack.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }


    //Modifier reclamation pour utilisateur
    /**
     * @Route("reclamation/{id}/modifier", name="reclamation_modifier")
     */
    public function ModifierReclamation(Request $request, Reclamation $reclamation): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);

        $form->add('Modifier',SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('reclamation_Liste');
        }

        return $this->render('reclamation/reclamationModifier.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    //Modifier reclamation pour Admin
    /**
     * @Route("admin/reclamation/{id}/modifier", name="reclamation_modifier_back")
     */
    public function ModifierReclamationBack(Request $request, Reclamation $reclamation): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);

        $form->add('etatReclamation', ChoiceType::class, [
            'choices'=>[ 'En Cours' =>'En Cours',
                'Résolue'=>'Résolue'],

        ]);
        $form->add('utilisateur',EntityType::class,[
                'class'=> Utilisateur::class,
                'choice_label' =>'nom',

        ]);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('reclamation_Liste_back');
        }

        return $this->render('reclamation/reclamationModifierBack.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("reclamation/{id}/supprimer", name="reclamation_supprimer")
     */
    public function SupprimerReclamation($id)
    {
        $reclamation =$this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($reclamation);
        $entityManager->flush();
        return $this->redirectToRoute("reclamation_Liste");
    }

    /**
     * @Route("admin/reclamation/supprimer/{id}", name="reclamationSupprimerBack")
     */
    public function SupprimerReclamationBack($id):Response
    {
        $rep =$this->getDoctrine()->getRepository(Reclamation::class);
        $reclamation = $rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($reclamation);
        $entityManager->flush();
        return $this->redirectToRoute("reclamation_Liste_back");
    }
}
