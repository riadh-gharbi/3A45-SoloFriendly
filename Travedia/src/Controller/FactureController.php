<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Planning;
use App\Entity\Utilisateur;
use App\Form\FactureType;
use App\Form\PaiementBackType;
use App\Repository\FactureRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
{
    //Creation d'une facture
    //En principe cette étape doit être automatique.
    //Cette fonction peut être utilisé par l'admin
    /**
     * @Route("admin/facture/create", name="createFacture")
     */
    public function createFacture(Request $request)
    {
        //PLACE HOLDER UNTIL MERGE WITH KARIM
        $rep = $this->getDoctrine()->getRepository(Planning::class);
        $planning=$rep->find(1);

        //PLACE HOLDER UNTIL MERGE WITH IBTIHEL
        $repUser = $this->getDoctrine()->getRepository(Utilisateur::class);
        $user = $repUser->findOneBy(['role'=>'User']);

        $guide = $repUser->findOneBy(['role'=>'Guide']);

        $facture = new Facture();
        $facture->setClient($user);
        $facture->setOwner($guide);
        $facture->setPlanning($planning);
        $facture->setPrix(200);
        $facture->setDateCreation(new \DateTime());

        //Le statut est en attente par défault. Le status changera dans ces deux cas
        // CAS 1: Paiement en ligne. L'API Stripe nous donnera le nouvel état et cela changera ce champs dans
        //      l'enregistrement de la base
        //CAS 2: Paiement cash. Le Guide aura le moyen de modifier l'état du paiement en recevant l'argent du voyageur
                //aprés le voyage. Il ne pourra pas modifier un paiement de type "En Ligne"
        $facture->setStatut("En Attente");
        $form=$this->createForm(FactureType::class,$facture);
        $form->add('Accepter',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($facture);
            $entityManager->flush();

            return $this->redirectToRoute('afficherFactureListeBack');

        }



        return $this->render('facture/newFacture.html.twig', [
            'controller_name' => 'FactureController', 'form'=>$form->createView()
        ]);

    }
    /**
     * @Route("/admin/facture/afficherListe" ,name="afficherFactureListeBack")
     */
    public function afficherFactureListeBack()
    {
        $factures=$this->getDoctrine()->getRepository(Facture::class)->findAll();
        return $this->render('facture/factureAfficherListBack.html.twig',['factures'=>$factures]);
    }

    /**
     * @Route("/facture/afficherListe" , name="afficherFactureListe")
     */
    public function afficherFactureListe()
    {

        $user=$this->getDoctrine()->getManager()->getRepository(Utilisateur::class)->find(1);
        //GET CURRENT USER
        if ($user->getRole()=='Admin'){
            return $this->redirectToRoute('afficherFactureListeBack');
        }else
        {

            //Utilisation du Strategy Pattern Ici au lien d'aller vers DQL ou query builder
            //Cause: DQL n'a pas voulu fonctionner
            //Query builder nous donne un faux résultat
            //A voir plus tard
            $criteria = new Criteria();
            $criteria->where(Criteria::expr()->contains("client",$user));
            $factures=$this->getDoctrine()->getRepository(Facture::class)->findBy(['owner'=>$user]);
            return $this->render('facture/factureAfficherListeUser.html.twig',['factures'=>$factures]);
        }
    }

    /**
     * @Route("/facture/afficher{id}" , name="afficherFacture")
     */
    public function afficherFacture($id)
    {
        $factureId= $this->getDoctrine()->getRepository(Facture::class)->find($id);
        return $this->render('facture/factureAfficher.html.twig', [
            'facture' => $factureId,
        ]);

    }

    /**
     * @Route("/admin/facture/afficher{id}" , name="afficherFactureBack")
     */
    public function afficherFactureBack($id)
    {
        $factureId= $this->getDoctrine()->getRepository(Facture::class)->find($id);
        return $this->render('facture/factureAfficherBack.html.twig', [
            'facture' => $factureId,
        ]);

    }




    /**
     * @Route("/facture/modifier{id}", name="modifierFactureFront")
     */
    public function modifierFactureFront($id, Request $request)
    {
        $facture=$this->getDoctrine()->getRepository(Facture::class)->find($id);
        $form= $this->createFormBuilder($facture)
            ->add('statut',ChoiceType::class,['choices'=>[
                'En Cours' =>'En Cours',
                'Effectué' => 'Effectué',
                'Annulé' => 'Annulé'
            ]])
            ->add('Modifier',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('afficherFacture' , ['id'=>$id]);

        }


        return $this->render('/facture/modifierFactureFront.html.twig',['form' =>$form->createView() , 'id'=>$id]);

    }

    /**
     * @Route("/admin/facture/modifier{id}" ,name="modifierFactureBack")
     */
    public function modifierFactureBack($id,Request $request)
    {
        $facture=$this->getDoctrine()->getRepository(Facture::class)->find($id);
        $form=$this->createForm(PaiementBackType::class, $facture);
        $form->add('Modifier', SubmitType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() &&$form->isValid() )
        {
            $entityManager =$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('afficherFactureBack',['id'=>$id]);

        }

        return $this->render('/facture/modifierFactureBack.html.twig',['form'=>$form->createView()]);

    }


    /**
     * @Route("/facture/supprimer{id}" ,name="supprimerFacture")
     */
    public function supprimerFacture($id)
    {
        $facture =$this->getDoctrine()->getRepository(Facture::class)->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($facture);
        $entityManager->flush();

        return $this->redirectToRoute("afficherFactureListeBack");

    }

}
