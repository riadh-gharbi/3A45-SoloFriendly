<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Poste;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\CommentaireType;

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

}
