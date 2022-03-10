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
//use http\Env\Url;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\Collection;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;


class PaiementController extends AbstractController
{
    //Creation d'une paiement
    //En principe cette étape doit être automatique.
    //Cette fonction peut être utilisé par l'admin
    /**
     * @Route("admin/paiement/create", name="createFacture")
     * @throws ApiErrorException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function createFacture(Request $request,MailerInterface $mailer)
    {
        $this->checkUpdatePaiement($mailer);

        //PLACE HOLDER UNTIL MERGE WITH KARIM
        //Integration done, just add choice of plannings
        //$rep = $this->getDoctrine()->getRepository(Planning::class);
        //$planning=$rep->find(1);

        //PLACE HOLDER UNTIL MERGE WITH IBTIHEL
        //Integration done, juste add choice of users
        //As this is the back office side, admin can edit and add any kind of planning
        //$repUser = $this->getDoctrine()->getRepository(Utilisateur::class);
        //$user = $repUser->findOneBy(['role'=>'User']);

        //$guide = $repUser->findOneBy(['role'=>'Guide']);

        $paiement = new Paiement();
        //$paiement->setClient($user);
        //$paiement->setOwner($guide);
        //$paiement->setPlanning($planning);
        //$paiement->setPrix($planning->getPrix());
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
        Stripe::setApiKey('sk_test_51KT8ejAISKORykYshnnbQcDPyMdeStYUi7Xtp05Lh1or86C6AHB8K3NsvA6CmiFXv4obHCq1p3gxp8oV8YHNizZ000pllSDFVs');


        if($form->isSubmitted() && $form->isValid())
        {
            $planning =$form->get('planning')->getData();
            $paiement->setPrix($planning->getPrix());
            switch($form->get('typePaiement')){
                case 'En Ligne':
                    $session = Session::create([
                        'line_items' => [[
                            'price_data' => [
                                'currency' => 'eur',
                                'product_data' => [
                                    'name' => 'planning' . strval($planning->getId()),
                                ],
                                'unit_amount' => $paiement->getPrix(),
                            ],
                            'quantity' => 1,
                        ]],
                        'mode' => 'payment',
                        'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ]);
                    $paiement->setSessionID($session->id);
                    break;
                case 'Cash':
                    //Send SMS

                    //sms didn't work, using email instead
                    $email = ((new TemplatedEmail()))
                        ->from(new Address('Paiement@Travedia.com', 'Travedia Demande Paiement'))
                        ->to($paiement->getClient()->getEmail())
                        ->subject('Vous devez payez votre guide')
                        ->text('Votre paiement de  '.strval($paiement->getPrix()).'Dinar doit être payée dans les plus courts délais pour votre guide'.strval($paiement->getGuide()->getNom()).'. Sinon vous pouvez passer chez nous et nous lui passeront le paiement');
                    $mailer->send($email);
                    break;
            }
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($paiement);
            $entityManager->flush();

            if($session){
            return $this->redirect($session->url,303);}else{
                return $this->redirectToRoute('success_url');
            }

        }





        return $this->render('paiement/newFacture.html.twig', [
            'controller_name' => 'PaiementController', 'form'=>$form->createView(), 'paiement'=>$paiement
        ]);

    }

    /**
     * @Route("/checkout/success_url" , name="success_url")
     */
    public function successCheckout(MailerInterface $mailer):Response
    {


        $this->checkUpdatePaiement($mailer);

        return $this->render('paiement/success.html.twig',[]);

    }

    public function redirectAfterTime($url)
    {
        $response = new Response();

        $response->setStatusCode(200);
        $response->headers->set('Refresh', '5; url='. $url);

        $response->send();
        return $response;
    }
    /**
     * @Route("/checkout/cancel_url" , name="cancel_url")
     */
    public function cancelCheckout():Response
    {
        return $this->render('paiement/cancel.html.twig',[]);

    }


    /**
     * @Route("/paiement/create/{id}", name="createFactureFront")
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws ApiErrorException
     */
    public function createFactureFront(Request $request,$id,MailerInterface $mailer):Response
    {
        $this->checkUpdatePaiement($mailer);
        Stripe::setApiKey('sk_test_51KT8ejAISKORykYshnnbQcDPyMdeStYUi7Xtp05Lh1or86C6AHB8K3NsvA6CmiFXv4obHCq1p3gxp8oV8YHNizZ000pllSDFVs');
        $session=null;
        //PLACE HOLDER UNTIL MERGE WITH KARIM
        //Integrated with Karim, button in planning gives reference of planning to this function to use it
        $rep = $this->getDoctrine()->getRepository(Planning::class);
        $planning=$rep->find($id);

        //PLACE HOLDER UNTIL MERGE WITH IBTIHEL
        //Integrated with Ibtihel, gets reference by current user
        //Needs to be a USER
        $repUser = $this->getDoctrine()->getRepository(Utilisateur::class);
        //$user = $repUser->findOneBy(['role'=>'User']);
        $user = $repUser->findOneBy(['email'=>$this->getUser()->getUsername()]);

        $guide = $planning->getUtilisateur();

        $paiement = new Paiement();
        $paiement->setClient($user);
        //$recControl=ReclamationController::class;


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
        $form=$this->createForm(PaiementType::class,$paiement);
        $form->add('Checkout',SubmitType::class);
        $form->handleRequest($request);
        //dd($paiement->getOwner()->getEmail());

        if($form->isSubmitted() && $form->isValid())
        {
            //Faire la redirection Stripe si paiement en ligne
            switch($form->get('typePaiement')->getData()){
                case 'En Ligne':
                $session = Session::create([
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => 'planning' . strval($planning->getId()),
                            ],
                            'unit_amount' => $paiement->getPrix(),
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ]);
            $paiement->setSessionID($session->id);
                    return $this->redirect($session->url,303);
            break;
                case 'Cash':
                    //Send SMS
                    //$sms= new Sms(+21624625004,'Vous devez préparer'.strval($paiement->getPrix()).'Dinars pour payer votre guide');
                    //$provider= $providerManager->getProvider('messagebird.com');

                    //$provider->send($sms);
                    //sms didn't work, will use mail instead
                    $email = ((new TemplatedEmail()))
                        ->from(new Address('Paiement@Travedia.com', 'Travedia Demande Paiement'))
                        ->to($paiement->getClient()->getEmail())
                        ->subject('Vous devez payez votre guide')
                        ->text('Votre paiement de  '.strval($paiement->getPrix()).'Dinar doit être payée dans les plus courts délais pour votre guide'.strval($paiement->getClient()->getNom()).'. Sinon vous pouvez passer chez nous et nous lui passeront le paiement');
                    $mailer->send($email);

                    return $this->redirectToRoute('success_url');
                    break;
                case '':
                    return $this->redirectToRoute('cancel_url');
                }
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($paiement);
            $entityManager->flush();








        }



        return $this->render('paiement/newFactureFront.html.twig', [
            'controller_name' => 'PaiementController', 'form'=>$form->createView()
        ]);

    }

    /**
     * @throws ApiErrorException
     */
    public function checkUpdatePaiement(MailerInterface $mailer)
    {
        $rep = $this->getDoctrine()->getRepository(Paiement::class);
        $paiements=$rep->findAll();
        $stripe= new  \Stripe\StripeClient('sk_test_51KT8ejAISKORykYshnnbQcDPyMdeStYUi7Xtp05Lh1or86C6AHB8K3NsvA6CmiFXv4obHCq1p3gxp8oV8YHNizZ000pllSDFVs');
        foreach($paiements as $p)
        {
            $session =$stripe->checkout->sessions->retrieve($p->getSessionID());
            $em=$this->getDoctrine()->getManager();
            switch ($session->status)
            {
                case 'unpaid':
                    //no update
                    //send sms
                    $email = ((new TemplatedEmail()))
                        ->from(new Address('Paiement@Travedia.com', 'Travedia Demande Paiement'))
                        ->to($p->getClient()->getEmail())
                        ->subject('Vous devez payez votre guide')
                        ->text('Votre paiement de  '.strval($p->getPrix()).'Dinar doit être payée dans les plus courts délais pour votre guide'.strval($p->getClient()->getNom()).'. Sinon vous pouvez passer chez nous et nous lui passeront le paiement');
                    $mailer->send($email);
                    break;
                case 'paid':
                    //update db
                    $p->setStatut('Effectué');
                    $em->flush();
                    $email = ((new TemplatedEmail()))
                        ->from(new Address('Paiement@Travedia.com', 'Travedia Confirmation Paiement'))
                        ->to($p->getOwner()->getEmail())
                        ->subject('Reçu de paiement de la part de '.strval($p->getClient()->getNom()))
                        ->text('Voici le votre reçu de paiement de la somme de '.strval($p->getPrix()).'. Veuillez passer chez nous pour récupérer votre argent');
                    $mailer->send($email);
                    break;
                case 'no_payment_required':
                    //send sms


                    break;
            }
        }
    }

    /**
     * @Route("/admin/paiement/afficherListe" ,name="afficherFactureListeBack")
     */
    public function afficherFactureListeBack(Request $request,PaginatorInterface $paginator,MailerInterface $mailer)
    {
        $this->checkUpdatePaiement($mailer);

        $factures=$this->getDoctrine()->getRepository(Paiement::class)->findAll();
        $factures=$paginator->paginate(
            $factures,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 3)/*limit per page*/
        );
        return $this->render('paiement/factureAfficherListBack.html.twig',['factures'=>$factures]);
    }

    /**
     * @Route("/paiement/afficherListe" , name="afficherFactureListe")
     */
    public function afficherFactureListe(Request $request,MailerInterface $mailer)
    {
        $this->checkUpdatePaiement($mailer);

        //$user=$this->getDoctrine()->getManager()->getRepository(Utilisateur::class)->find(1);
        //GET CURRENT USER
        //if ($this->getUser()->getRoles()==['ROLE_ADMIN']){
          //  return $this->redirectToRoute('afficherFactureListeBack');
        //}else
        //{

            //Utilisation du Strategy Pattern Ici au lien d'aller vers DQL ou query builder
            //Cause: DQL n'a pas voulu fonctionner
            //Query builder nous donne un faux résultat
            //A voir plus tard
        $repUser = $this->getDoctrine()->getRepository(Utilisateur::class);
        //$user = $repUser->findOneBy(['role'=>'User']);
        $user = $repUser->findOneBy(['email'=>$this->getUser()->getUsername()]);
            $criteria = new Criteria();
            //WHEN INTEGRATING WITH IBTIHEL, ADD IF STATEMENT TO CHECK IF VOYAGEUR OR GUIDE TO CHANGE THE CRITERIA EXPRESSION

            $criteria->where(Criteria::expr()->eq('owner',$user))
            ->orWhere(Criteria::expr()->eq('client',$user));
            $paiements=(Object)$this->getDoctrine()->getRepository(Paiement::class)->matching($criteria);



            return $this->render('paiement/factureAfficherListeUser.html.twig',['factures'=>$paiements]);
        //}
    }

    /**
     * @Route("/paiement/afficher{id}" , name="afficherFacture")
     */
    public function afficherFacture($id,MailerInterface $mailer)
    {
        $this->checkUpdatePaiement($mailer);

        $factureId= $this->getDoctrine()->getRepository(Paiement::class)->find($id);
        return $this->render('paiement/factureAfficher.html.twig', [
            'paiement' => $factureId,
        ]);

    }

    /**
     * @Route("/admin/paiement/afficher{id}" , name="afficherFactureBack")
     */
    public function afficherFactureBack($id,MailerInterface $mailer)
    {
        $this->checkUpdatePaiement($mailer);

        $factureId= $this->getDoctrine()->getRepository(Paiement::class)->find($id);
        return $this->render('paiement/factureAfficherBack.html.twig', [
            'paiement' => $factureId,
        ]);

    }




    /**
     * @Route("/paiement/modifier{id}", name="modifierFactureFront")
     */
    public function modifierFactureFront($id, Request $request,MailerInterface $mailer)
    {
        $this->checkUpdatePaiement($mailer);

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
    public function modifierFactureBack($id,Request $request,MailerInterface $mailer)
    {
        $this->checkUpdatePaiement($mailer);

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
