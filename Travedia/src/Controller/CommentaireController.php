<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Poste;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\CommentaireType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CommentaireController extends AbstractController

{
    /**
     * @Route("/commentaire", name="commentaire")
     */
    public function index(): Response
    {
        return $this->render('poste/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route ("/Poste/ajouterCommentaire{id}",name="ajouterCommentaire")
     */
    function AjoutCommentaire(Request $request,$id) {
        $commentaire=new Commentaire();
        $commentaire->setDate(new \DateTime());
        $form=$this->createForm(CommentaireType::class,$commentaire);
        $form->add("Ajouter",SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $rep=$this->getDoctrine()->getRepository(Poste::class);
            $poste=$rep->find($id);
            $poste->addCommentaire($commentaire);
            $em=$this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();
            return $this->redirectToRoute('afficherPoste');
        }
        return $this->render("poste/ajouterCommentaire.html.twig",["f"=>$form->createView()]);
    }

    /**
     * @Route("/supprimerCommentaire/{id}", name="supprimerCommentaire")
     * Suppression d'un Commentaire
     */
    public function SupprimerCommentaire($id): Response
    {
        $commentaire = $this->getDoctrine()->getRepository(Commentaire::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($commentaire);
        $entityManager->flush();
        return $this->redirectToRoute('afficherPoste');

    }


/**
 * @param CommentaireRepository $repository
 * @param $id
 * @param $request
 * @Route("/Poste/modifierCommentaire/{id}", name="modifierCommentaire")
 * Modifier un poste
 */
public function modifierCommentaire(Request $request, $id): Response
{
    $commentaire = $this->getDoctrine()->getRepository(Commentaire::class)->find($id);
    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->add("Modifier",SubmitType::class);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute('afficherPoste');
    }
    return $this->render("poste/modifierCommentaire.html.twig",[
        "commentaire"=>$commentaire,
        "f" => $form->createView(),
    ]);
}

    /**
     * @Route("/Poste/ajouterComJSON", name="ajoutc_json")
     */
    public function ajoutPosteJSON(Request $request) {

        $id = $request->query->get("id");
        $poste_id = $request->query->get("poste_id");
        $contenu = $request->query->get("contenu");
        $date = $request->query->get("date");


        $commentaire=new Commentaire();
        $commentaire->setContenu($contenu);
        $commentaire->setDate(new \DateTime());
        //$poste->setProfile("profile_id");

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            return new JsonResponse("commentaire created", 200);
        }catch (\Exception $ex) {
            return new Response("exception".$ex->getMessage());
        }

    }


    /**
     * @Route("/Poste/afficherComJSON", name="affc_json")
     */
    public function affichercomJson(CommentaireRepository $rep): Response
    {
        $commentaire=$rep->findAll();
        $categorieList =[];
        foreach ($commentaire as $commentaire ){
            $categorieList[] = [
                'id' => $commentaire->getId(),
                'contenu' => $commentaire->getContenu(),
                'date' => $commentaire->getDate()

            ];

        }
        return new Response(json_encode($categorieList));

        $encoders = [ new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $json=$serializer->serialize($poste, 'json',['circular_reference_handler'=>function ($object){return $object->getId();
        }
        ]);
        $response=new Response($json);
        $response->headers->set('Content-Type','application/json');
        return $response;
    }


    /**
     * @Route("/Poste/modifierComJSON", name="editc_json")
     */

    public function  modifierComJSON(Request $request) {
        $id= $request->get("id");
        $poste_id = $request->query->get("poste_id");
        $contenu = $request->query->get("contenu");
        $em=$this->getDoctrine()->getManager();
        $commentaire = $em->getRepository(Commentaire::class)->find($id);
        $commentaire->setContenu($contenu);


        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();

            return new JsonResponse("sucess", 200);
        }catch (\Exception $ex) {
            return new Response("failed".$ex->getMessage());
        }
    }

    /**
     * @Route("/Poste/supprimercomJSON",name="suppcJson")
     */
    public function deleteEvenementesJson(Request $request, CommentaireRepository $rep)
    {
        $commentaire = $rep->find($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($commentaire);
        $em->flush();
        try {
            return new JsonResponse("comment deleted", 200);
        }catch (\Exception $ex) {
            return new Response("exception".$ex->getMessage());
        }
    }

}
