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
use Dompdf\Dompdf;
use Dompdf\Options;
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
            $this->addFlash('success', 'poste Created!' );
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

    /**
     * @param PosteRepository $repository
     * @param CommentaireRepository $repositoryC
     * @return Response
     * @Route("/Poste/afficherAct", name="afficherPoste")
     */
    public function recherchePoste(Request $request )
    {
        $em=$this->getDoctrine()->getManager();
        $poste = $em->getRepository(Poste::class)->findAll();
        if($request->isMethod("POST"))
        {
            $search = $request->get('search');
            $poste = $em->getRepository(Poste::class)->findBy(array('contenu'=>$search));

        }

        return $this->render    ("poste/afficherPoste.html.twig",
            ["poste"=>$poste ]);

    }
    /**
     * @param poste $poste
     * @Route("/print/{id}", name="print")
     */
    public function print($id)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
//        $pdfOptions->set('isRemoteEnabled', true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $repository = $this->getDoctrine()->getRepository(Poste::class);
        $poste = $repository->findOneByid($id);



        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('poste/pdf.html.twig', [
            "poste"=>$poste ,

        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);


        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("poste.pdf", [
            "Attachment" => false
        ]);
    }
    /**
     * @Route("/triLike_poste", name="tri_nbr_like")
     */
    public function TriIDPOSTE()
    {
        $poste= $this->getDoctrine()->getRepository(Poste::class)->TriParLike();
        return $this->render("poste/afficherPoste.html.twig",array('poste'=>$poste));
        return $this->render    ("poste/afficherPoste.html.twig",
            ["poste"=>$poste ]);
    }
    /**
     * @Route("/triLike_posteD", name="tri_nbr_likeD")
     */
    public function TriIDPOSTED()
    {
        $poste= $this->getDoctrine()->getRepository(Poste::class)->TriParLikeD();
        return $this->render("poste/afficherPoste.html.twig",array('poste'=>$poste));
        return $this->render    ("poste/afficherPoste.html.twig",
            ["poste"=>$poste ]);
    }



}