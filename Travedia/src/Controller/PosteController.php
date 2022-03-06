<?php

namespace App\Controller;


use App\Repository\CommentaireRepositoryRepository;
use App\Entity\Commentaire;
use App\Entity\Poste;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\PosteType;
class PosteController extends AbstractController

{
    /**
     * @Route("/poste", name="poste")
     */
    public function index(): Response
    {
        return $this->render('poste/index.html.twig', [
            'controller_name' => 'PosteController',
        ]);
    }

    
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route ("/Poste/ajouterPoste",name="ajouterPoste")
     */
    function AjoutPoste(Request $request){
        $poste=new Poste();
        $poste->setDate(new \DateTime());
        $form=$this->createForm(PosteType::class,$poste);
        $form->add("Ajouter",SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $poste->setImage($newFilename);
            }
            $em=$this->getDoctrine()->getManager();
            $em->persist($poste);
            $em->flush();
            return $this->redirectToRoute('afficherPoste');
        }
        return $this->render("poste/ajouterPoste.html.twig",["f"=>$form->createView()]);
    } 
    /**
     * @param PosteRepository $repository
     * @param CommentaireRepository $repositoryC
     * @return Response
     * @Route("/Poste/afficherAct", name="afficherPoste")
     */
    public function AfficherPoste(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Poste::class);
        $repositoryC = $this->getDoctrine()->getRepository(Commentaire::class);
        $poste = $repository->findAll();
        $commentaire = $repositoryC->findAll();
        return $this->render    ("poste/afficherPoste.html.twig",
            ["poste"=>$poste ,
            "commentaire"=>$commentaire]);

        
    }


   // /**
    // * @param PosteRepository $rep
   //  * @return Response
   //  * @Route("/Poste/affichertest/{id}", name="commentaire")
    // */
   // public function affcat(PosteRepository $rep, $id)
   // {

      //  $commentaire=$rep->findAll();
     //   return $this->render('Poste/commentaire.html.twig', [
      //      'commentaire' => $commentaire,
        //    'commentaireid' => $rep->getCommentairebyid($id),


        //]);

   // }




    /**
     * @param PosteRepository $repository
     * @param $id
     * @param $request
     * @Route("/Poste/modifierPoste/{id}", name="modifierPoste")
     * Modifier un poste
     */
    public function modifierPoste(Request $request, $id): Response
    {
        $poste = $this->getDoctrine()->getRepository(Poste::class)->find($id);
        $form = $this->createForm(PosteType::class, $poste);
        $form->add("Modifier",SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $poste->setImage($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('afficherPoste');
        }
        return $this->render("poste/modifierPoste.html.twig",[
            "poste"=>$poste,
            "f" => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprimerPoste/{id}", name="supprimerPoste")
     * Suppression d'un Poste
     */
    public function SupprimerPoste($id): Response
    {
        $poste = $this->getDoctrine()->getRepository(Poste::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($poste);
        $entityManager->flush();
        return $this->redirectToRoute('afficherPoste');

    }
  
}