<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Utilisateur;
use App\Entity\ReclamationReponse;
use App\Form\ReclamationReponseType;
use App\Form\ReclamationType;
use App\Repository\ReclamationReponseRepository;
use App\Repository\ReclamationRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Serializer;



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
    public function AjouterReclamation(Request $request): Response
    {
        //PLACEHOLDER FOR UTILISATEUR
        $util = new Utilisateur();
        //$util = $this->getUser();
        $utilRep= $this->getDoctrine()->getRepository(Utilisateur::class);
        $util = $utilRep->findOneBy(['email'=>$this->getUser()->getUsername()]);
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
        $util = $utilRep->findOneBy(['email'=>$this->getUser()->getUsername()]);
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
        $reclamationRep=$this->getDoctrine()->getRepository(ReclamationReponse::class)->findOneBy(['reclamation'=>$reclamation]);
        return $this->render('reclamation/reclamationAfficher.html.twig', [
            'reclamation' => $reclamation, 'reclamationRep'=>$reclamationRep,
        ]);
    }

    /**
     * @Route("admin/reclamation/{id}", name="reclamation_afficher_back")
     */
    public function afficherReclamationBack($id): Response
    {
        //Afficher avec les réponses des reclamations

        $reclamationExiste = false;
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        if ($this->getDoctrine()->getRepository(ReclamationReponse::class)->findOneBy(['reclamation'=>$reclamation]) !=null)
        {
            $reclamationExiste=true;
        }
        $reclamationResponse=new ReclamationReponse();

        $reclamationResponse->setReclamation($reclamation);
        $form=$this->createForm(ReclamationReponseType::class);
        $form->add("Envoyer",SubmitType::class);
        if($form->isSubmitted()&& $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($reclamationResponse);
            $em->flush();
            $this->redirectToRoute('reclamation_afficher_back', ['reclamation'=>$reclamation , 'reclamationResponse'=>$reclamationResponse]);
        }
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        return $this->render('reclamation/reclamationAfficherBack.html.twig', [
            'reclamation' => $reclamation, 'form'=>$form->createView(), 'reclamationExiste' =>$reclamationExiste,
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
    public function SupprimerReclamationBack($id, ReclamationRepository $rep):Response
    {
        //$reclamation =$this->getDoctrine()->getRepository(ReclamationRepository::class)->find($id);
        //$reclamation = $rep->find($id);
        $reclamation = $rep->find($id);
        $reponse = $this->getDoctrine()->getRepository(ReclamationReponse::class)->find($reclamation->getReclamationRep());

        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($reclamation->getReclamationRep());
        $entityManager->remove($reclamation);
        $entityManager->flush();
        return $this->redirectToRoute("reclamation_Liste_back");
    }


    /**
     * @Route("/afficherReclamations" , name="afficherReclamationJson")
     */
    public function afficherReclamationJson(ReclamationRepository $rep, SerializerInterface $serializer): Response
    {
       $reclamations=$rep->findAll();
        //$json = $serializer->serialize($reclamations,'json',['groups'=>'reclamations']);
        //dump($json);
        //die;
        //return new Response(json_encode($json));

        $encoders = [ new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $json=$serializer->serialize($reclamations, 'json',['circular_reference_handler'=>function ($object){return $object->getId();
        }
        ]);

        $response=new Response($json);
        $response->headers->set('Content-Type','application/json');

        return $response;
    }

    /**
     * @Route("/getReclamationByID" , name="afficherReclamationByIDJson")
     */
    public function afficherReclamationByIDJson(Request $req,ReclamationRepository $rep, SerializerInterface $serializer): Response
    {
        $id=$req->query->get('id');
        $reclamation=$rep->find($id);
        //$json = $serializer->serialize($reclamations,'json',['groups'=>'reclamations']);
        //dump($json);
        //die;
        //return new Response(json_encode($json));

        $encoders = [ new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $json=$serializer->serialize($reclamation, 'json',['circular_reference_handler'=>function ($object){return $object->getId();
        }
        ]);

        $response=new Response($json);
        $response->headers->set('Content-Type','application/json');

        return $response;
    }

    /**
     * @param ReclamationRepository $rep
     * @param SerializerInterface $serializer
     * @Route("/ajouterReclamation" , name="ajouterRecJSON")
     */
    public function ajouterReclamationJson(Request $request, ReclamationRepository $rep, SerializerInterface $serializer,NormalizerInterface $normalizer,UtilisateurRepository $repU):Response
    {
        try{
        $reclamation= new Reclamation();
        $reclamation->setSujet($request->get('sujet'));
        $reclamation->setContenu($request->get('contenu'));
        //$reclamation->setUtilisateur($request->get('utilisateurID'));
        if ($repU->find($request->get('utilisateurID'))!=null)
        $reclamation->setUtilisateur($repU->find($request->get('utilisateurID')));
        else $reclamation->setUtilisateur(null);
        $reclamation->setEtatReclamation($request->get('etatReclamation'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($reclamation);
        $em->flush();
        $encoders= [new JsonEncoder()];
        $normalizers=[new ObjectNormalizer()];
        $serializer =new Serializer($normalizers,$encoders);
        $json=$normalizer->normalize($reclamation,'json',['groups'=>'post:read']);
        return new Response(json_encode($json),200);}
        catch (IOException $exception){new Response ("Problem",500);}

    }

    /**
     * @param Request $request
     * @param NormalizerInterface $normalizer
     * @Route("/ajouterReclamationReponse" , name="ajouterReclamationReponse")
     */
    public function AjouterReclamationReponseJson(Request $request,NormalizerInterface $normalizer,ReclamationRepository $recRep,ReclamationReponseRepository $repRep):Response
    {
        $reclamationReponse = new ReclamationReponse();
        $reclamationReponse->setContenu($request->get('contenu'));
        $reclamation = $recRep->find($request->get('reclamationId'));
        $reclamationReponse->setReclamation($reclamation);
        $em=$this->getDoctrine()->getManager();
        $em->persist($reclamationReponse);
        $em->flush();
        $reclamation->setReclamationRep($reclamationReponse);
        $em->flush();
        $encoders= [new JsonEncoder()];
        $normalizers=[new ObjectNormalizer()];
        $serializer =new Serializer($encoders);
        $json=$normalizer->normalize($reclamation,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }





    /**
     * @param Request $request
     * @param ReclamationRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @param UtilisateurRepository $repU
     * @return Response
     * @throws ExceptionInterface
     * @Route("/modifierReclamation", name="modifierReclamationJson")
     */
    public function modifierReclamationJson(Request $request,ReclamationRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer,UtilisateurRepository $repU):Response
    {
        $reclamation = $rep->find($request->get('id'));
        $reclamation->setContenu($request->get('contenu'));
        $reclamation->setUtilisateur($repU->find($request->get('utilisateurID')));

        $reclamation->setEtatReclamation($request->get('etatReclamation'));
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $json=$normalizer->normalize($reclamation,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

    /**
     * @param Request $request
     * @param ReclamationReponseRepository $repRep
     * @param NormalizerInterface $normalizer
     * @Route("/modifierReclamationReponse",name="modifierReclamationReponse")
     */
    public function ModifierReclamationReponse(Request $request,ReclamationReponseRepository $repRep, NormalizerInterface $normalizer)
    {
        $reclamationReponse= $repRep->find($request->get('id'));
        $reclamationReponse->setContenu($request->get('contenu'));
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $json=$normalizer->normalize($reclamationReponse,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

    /**
     * @param Request $request
     * @param ReclamationRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws ExceptionInterface
     * @Route("/deleteReclamation",name="deleteReclamationJson")
     */
    public function deleteReclamationJson(Request $request,ReclamationRepository $rep,ReclamationReponseRepository $resRep,SerializerInterface $serializer,NormalizerInterface $normalizer)
    {
        $reclamation = $rep->find($request->get('id'));
        //$response = $resRep->findBy(['reclamation'=>$reclamation]);
        $em = $this->getDoctrine()->getManager();
        $em->remove($reclamation);
        $em->flush();
        $json=$normalizer->normalize($reclamation,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

    /**
     * @param Request $request
     * @param ReclamationReponseRepository $repRep
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws ExceptionInterface
     * @Route("/deleteReclamationRep", name="deleteReclamationRep")
     */
    public function deleteReclamationReponseJson(Request $request,ReclamationReponseRepository $repRep,NormalizerInterface $normalizer)
    {
        $reclamationReponse = $repRep-find($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($reclamationReponse);
        $em->flush();
        $json=$normalizer->normalize($reclamationReponse,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }
}
