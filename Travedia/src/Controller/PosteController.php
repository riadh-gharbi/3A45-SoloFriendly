<?php

namespace App\Controller;


use App\Entity\Profile;
use App\Entity\Utilisateur;
use App\Repository\CommentaireRepository;
use App\Entity\Commentaire;
use App\Entity\Poste;
use App\Repository\PosteRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\PosteType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
            $repU=$this->getDoctrine()->getRepository(Utilisateur::class);
            $repP=$this->getDoctrine()->getRepository(Profile::class);
            $user=$repU->findOneBy(['email'=>$this->getUser()->getUsername()]);
            $profile=$repP->findOneBy(['utilisateur'=>$user->getId()]);
            $poste->setProfile($profile);
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

    /**
     * @param Request $request
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @Route("/Poste/ajouterPosteJSON", name="ajoutPosteJson")
     */
    public function ajoutPosteJSON(Request $request, NormalizerInterface $normalizer):Response {

        //$id = $request->query->get("id");
        $profile_id = $request->query->get("userId");
        $contenu = $request->query->get("contenu");
        //$image = $request->query->get("image");
        $date = $request->query->get("date");
        $likes = $request->query->get("react");

        $poste=new Poste();
        $poste->setContenu($contenu);
        $poste->setLikes($likes);
        $poste->setDate(new \DateTime());
        //$poste->setImage($image);
        //$poste->setProfile($profile_id);


            $em = $this->getDoctrine()->getManager();
            $em->persist($poste);
            $em->flush();

            $encoders= [new JsonEncoder()];
            $normalizers=[new ObjectNormalizer()];
            $serializer =new Serializer($normalizers,$encoders);
            $json=$normalizer->normalize($poste,'json',['groups'=>'post:read']);
            return new Response(json_encode($json));


    }

    /**
     * @Route("/Poste/afficherPosteJSON", name="aff_json")
     */
    public function afficherposteJson(PosteRepository $rep): Response
    {
        $poste=$rep->findAll();
        $categorieList =[];
        foreach ($poste as $poste ){
            $categorieList[] = [
                'id' => $poste->getId(),
                'contenu' => $poste->getContenu(),
                'image' => $poste->getImage(),
                'likes' => $poste->getLikes(),
                'date' => $poste->getDate()->format('Y-m-d'),
                'profile'=>$poste->getProfile()->getId()
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
     * @Route("/Poste/modifierPosteJSON", name="edit_json")
     */

    public function  modifierPosteJSON(Request $request) {
        $id= $request->get("id");
        $profile_id = $request->query->get("profile_id");
        $contenu = $request->query->get("contenu");
        $image = $request->query->get("image");
        $date = $request->query->get("date");
        $likes = $request->query->get("likes");
        $em=$this->getDoctrine()->getManager();
        $poste = $em->getRepository(Poste::class)->find($id);
        if($request->files->get("image")!=null){

            $file = $request->files->get("image");
            $fileName = $file->getClientOriginalName();
            $file->move(
                $fileName
            );
            $poste->setPhoto($fileName);


        }
        $poste->setContenu($contenu);
        $poste->setlikes($likes);

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($poste);
            $em->flush();

            return new JsonResponse("sucess", 200);
        }catch (\Exception $ex) {
            return new Response("failed".$ex->getMessage());
        }
    }


    /**
     * @Route("/Poste/supprimerPosteJSON",name="suppPosteJson")
     */
    public function deleteEvenementesJson(Request $request, PosteRepository $rep)
    {
        $poste = $rep->find($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($poste);
        $em->flush();
        try {
            return new JsonResponse("Poste deleted", 200);
        }catch (\Exception $ex) {
            return new Response("exception".$ex->getMessage());
        }
    }
}