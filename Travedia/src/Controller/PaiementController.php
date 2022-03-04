<?php

namespace App\Controller;

use App\Entity\Paiement;
use App\Entity\Planning;
use App\Entity\Utilisateur;
use App\Form\PaiementType;
use App\Form\PaiementBackType;
use App\Repository\PaiementRepository;
use App\Repository\PlanningRepository;
use Doctrine\Common\Collections\Criteria;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class PaiementController extends AbstractController
{
    //Creation d'une paiement
    //En principe cette étape doit être automatique.
    //Cette fonction peut être utilisé par l'admin
    /**
     * @Route("admin/paiement/create", name="createFacture")
     * @throws ApiErrorException
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

        $paiement = new Paiement();
        $paiement->setClient($user);
        $paiement->setOwner($guide);
        $paiement->setPlanning($planning);
        $paiement->setPrix($planning->getPrix());
        $paiement->setDateCreation(new \DateTime());

        //Le statut est en attente par défault. Le status changera dans ces deux cas
        // CAS 1: Paiement en ligne. L'API Stripe nous donnera le nouvel état et cela changera ce champs dans
        //      l'enregistrement de la base
        //CAS 2: Paiement cash. Le Guide aura le moyen de modifier l'état du paiement en recevant l'argent du voyageur
                //aprés le voyage. Il ne pourra pas modifier un paiement de type "En Ligne"
        $paiement->setStatut("En Cours");
        $form=$this->createForm(PaiementBackType::class,$paiement);
        $form->add('Checkout',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($paiement);
            $entityManager->flush();
            Stripe::setApiKey('sk_test_51KT8ejAISKORykYshnnbQcDPyMdeStYUi7Xtp05Lh1or86C6AHB8K3NsvA6CmiFXv4obHCq1p3gxp8oV8YHNizZ000pllSDFVs');
            $session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'planning'.strval($planning->getId()),
                        ],
                        'unit_amount' => $paiement->getPrix(),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('success_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('cancel_url',[], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);



            return $this->redirect($session->url,303);

        }



        return $this->render('paiement/newFacture.html.twig', [
            'controller_name' => 'PaiementController', 'form'=>$form->createView()
        ]);

    }

    /**
     * @Route("/checkout/success_url" , name="success_url")
     */
    public function successCheckout():Response
    {
        return $this->render('paiement/success.html.twig',[]);

    }
    /**
     * @Route("/checkout/cancel_url" , name="cancel_url")
     */
    public function cancelCheckout():Response
    {
        return $this->render('paiement/cancel.html.twig',[]);

    }


    /**
     * @Route("/paiement/create", name="createFactureFront")
     */
    public function createFactureFront(Request $request):Response
    {
        Stripe::setApiKey('sk_test_51KT8ejAISKORykYshnnbQcDPyMdeStYUi7Xtp05Lh1or86C6AHB8K3NsvA6CmiFXv4obHCq1p3gxp8oV8YHNizZ000pllSDFVs');

        //PLACE HOLDER UNTIL MERGE WITH KARIM
        $rep = $this->getDoctrine()->getRepository(Planning::class);
        $planning=$rep->find(1);

        //PLACE HOLDER UNTIL MERGE WITH IBTIHEL
        $repUser = $this->getDoctrine()->getRepository(Utilisateur::class);
        $user = $repUser->findOneBy(['role'=>'User']);

        $guide = $repUser->findOneBy(['role'=>'Guide']);

        $paiement = new Paiement();
        $paiement->setClient($user);
        $recControl=ReclamationController::class;


        $paiement->setOwner($guide);
        $paiement->setPlanning($planning);
        $paiement->setPrix(200);
        $paiement->setDateCreation(new \DateTime());

        //Le statut est en attente par défault. Le status changera dans ces deux cas
        // CAS 1: Paiement en ligne. L'API Stripe nous donnera le nouvel état et cela changera ce champs dans
        //      l'enregistrement de la base
        //CAS 2: Paiement cash. Le Guide aura le moyen de modifier l'état du paiement en recevant l'argent du voyageur
        //aprés le voyage. Il ne pourra pas modifier un paiement de type "En Ligne"
        $paiement->setStatut("En Cours");
        $form=$this->createForm(PaiementType::class,$paiement);
        $form->add('Checkout',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($paiement);
            $entityManager->flush();
            $session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'planning'.strval($planning->getId()),
                        ],
                        'unit_amount' => $paiement->getPrix(),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('success_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('cancel_url',[], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);



            return $this->redirect($session->url,303);


        }



        return $this->render('paiement/newFactureFront.html.twig', [
            'controller_name' => 'PaiementController', 'form'=>$form->createView()
        ]);

    }
    /**
     * @Route("/admin/paiement/afficherListe" ,name="afficherFactureListeBack")
     */
    public function afficherFactureListeBack()
    {
        $factures=$this->getDoctrine()->getRepository(Paiement::class)->findAll();
        return $this->render('paiement/factureAfficherListBack.html.twig',['factures'=>$factures]);
    }

    /**
     * @Route("/paiement/afficherListe" , name="afficherFactureListe")
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
            //WHEN INTEGRATING WITH IBTIHEL, ADD IF STATEMENT TO CHECK IF VOYAGEUR OR GUIDE TO CHANGE THE CRITERIA EXPRESSION
            $criteria->where(Criteria::expr()->eq('owner',$user));
            $factures=$this->getDoctrine()->getRepository(Paiement::class)->matching($criteria);
            return $this->render('paiement/factureAfficherListeUser.html.twig',['factures'=>$factures]);
        }
    }

    /**
     * @Route("/paiement/afficher{id}" , name="afficherFacture")
     */
    public function afficherFacture($id)
    {
        $factureId= $this->getDoctrine()->getRepository(Paiement::class)->find($id);
        return $this->render('paiement/factureAfficher.html.twig', [
            'paiement' => $factureId,
        ]);

    }

    /**
     * @Route("/admin/paiement/afficher{id}" , name="afficherFactureBack")
     */
    public function afficherFactureBack($id)
    {
        $factureId= $this->getDoctrine()->getRepository(Paiement::class)->find($id);
        return $this->render('paiement/factureAfficherBack.html.twig', [
            'paiement' => $factureId,
        ]);

    }




    /**
     * @Route("/paiement/modifier{id}", name="modifierFactureFront")
     */
    public function modifierFactureFront($id, Request $request)
    {
        $facture=$this->getDoctrine()->getRepository(Paiement::class)->find($id);
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


        return $this->render('/paiement/modifierFactureFront.html.twig',['form' =>$form->createView() , 'id'=>$id]);

    }

    /**
     * @Route("/admin/paiement/modifier{id}" ,name="modifierFactureBack")
     */
    public function modifierFactureBack($id,Request $request)
    {
        $facture=$this->getDoctrine()->getRepository(Paiement::class)->find($id);
        $form=$this->createForm(PaiementBackType::class, $facture);
        $form->add('Modifier', SubmitType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() &&$form->isValid() )
        {
            $entityManager =$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('afficherFactureBack',['id'=>$id]);

        }

        return $this->render('/paiement/modifierFactureBack.html.twig',['form'=>$form->createView()]);

    }


    /**
     * @Route("/paiement/supprimer{id}" ,name="supprimerFacture")
     */
    public function supprimerFacture($id)
    {
        $facture =$this->getDoctrine()->getRepository(Paiement::class)->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($facture);
        $entityManager->flush();

        return $this->redirectToRoute("afficherFactureListeBack");

    }

}
