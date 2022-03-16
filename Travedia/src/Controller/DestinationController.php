<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Entity\Region;
use App\Entity\RegionType;
use MercurySeries\FlashyBundle\FlashyNotifier;
//use MercurySeries\FlashyBundle\DependencyInjection\MercurySeriesFlashyExtension;

use App\Form\DestinationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\DestinationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert ; 

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Serializer;



class DestinationController extends AbstractController
{
//     public function __construct(FlashyNotifier $flashy)
// {
//     $this->flashy = $flashy;
// }
    /**
     * @Route("/destinationadd", name="destination")
     */
    public function add(Request $request, FlashyNotifier $flashy): Response
    {
        $destination = new Destination();

        $form = $this->createForm(DestinationType::class,$destination);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $destination->setImage($newFilename);
            }
          

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($destination);

            $entityManager->flush();


         
           $this->addFlash('success', 'destination a été créé');
        //   $flashy->success('destination created!', 'http://your-awesome-link.com');

            return $this->redirectToRoute('listeD');

        }

        return $this->render('destination/add.html.twig', [
            'destination'=>$destination,
              'form' => $form->createView(),
          ]);
    }

 /**
     * @Route("/test", name="test")
     */
    public function index(): Response
    {
        return $this->render('destination/test.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }

  /**
     * @Route("/destinationliste", name="listeD")
     */
    public function liste(Request $request,PaginatorInterface $paginator ):Response
          {
            $destination= $this->getDoctrine()->getRepository(Destination::class)->findAll();
            $rep=$this->getDoctrine()->getRepository(Destination::class);

            $destination = $paginator->paginate(
              $destination,
              $request->query->getInt('page', 1)/*page number*/,
              $request->query->getInt('limit', 3)/*limit per page*/

            );
            return $this->render('destination/index.html.twig',['destination'=> $destination]);
            }

            
 /** 
  * @Route("/destinationshow/{id}", name="destinationshow") 
  */ 
 public function show($id) 
 { $destination = $this->getDoctrine()->getRepository(Destination::class) ->find($id); 
    return $this->render('destination/showDestination.html.twig', ['destination' => $destination]);
 }


   
                /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/destinationedit/{id}", name="destinationedit")
     */
    public function editt($id,DestinationRepository $rep,Request $request):Response
    {

        $destination=$rep->find($id);
        $form = $this->createForm(DestinationType::class, $destination);
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
              $destination->setImage($newFilename);
          }
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('info', 'destination a été modifié');

            return $this->redirectToRoute('listeD');
        }

        return $this->render('destination/editDestination.html.twig', [
          'destination'=>$destination,
            'form' => $form->createView(),
        ]);
    }

/** 
 * @Route("/destinationdelete/{id}", name="destinationdelete") * @Method({"DELETE"})
 */
 public function delete(Request $request, $id) 
 { 
     $destination = $this->getDoctrine()->getRepository(Destination::class)->findOneById($id); 
     $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($destination);
       $entityManager->flush();
       $this->addFlash('error', 'destination a été supprimé');

         return $this->redirectToRoute('listeD'); 
 }

//  ******** FRONT **********

/**
     * @Route("/regionlistefront", name="listeRfront")
     */
    public function listerfront(Request $request,PaginatorInterface $paginator )
          {
            $regionfront= $this->getDoctrine()->getRepository(Region::class)->findAll();
       
            return $this->render('destinationFront/showRegionFront.html.twig',['regionfront'=> $regionfront]);
            }


            /**
     * @Route("/destinationlistefront", name="listeDfront")
     */
    public function listedfront(Request $request,PaginatorInterface $paginator )
    {
      $destinationfront= $this->getDoctrine()->getRepository(Destination::class)->findAll();
      $destinationfront = $paginator->paginate(
        $destinationfront,  
        $request->query->getInt('page', 1)/*page number*/,
        $request->query->getInt('limit', 3)/*limit per page*/
   );

      return $this->render('destinationFront/showDestinationFront.html.twig',['destinationfront'=> $destinationfront]);
      }
      /** 
  * @Route("/destinationshowfront/{id}", name="destinationshowfront") 
  */ 
 public function showf($id) 
 { $destinationfront = $this->getDoctrine()->getRepository(Destination::class) ->find($id); 
    return $this->render('destinationFront/showFront.html.twig', ['destinationfront' => $destinationfront]);
 }

//afficher liste des destination par region
  //       /**
  //    * @Route("/destinationparRegion", name="listeDPRfront")
  //    */
  //   public function listeDestParRegion(Request $request,PaginatorInterface $paginator, $id)
  //   {
      
  //     $destinationfront= $this->getDoctrine()->getRepository(Destination::class)->getDestinationsByRegionID($id);
  // //     $destinationfront = $paginator->paginate(
  // //       $destinationfront,  
  // //       $request->query->getInt('page', 1)/*page number*/,
  // //       $request->query->getInt('limit', 5)/*limit per page*/   
  // //  );
  //  return $this->render('destinationFront/destinationparRegion.html.twig',['destinationfront'=> $destinationfront]);
  //     }

/**
     * @param DestinationRepository $rep
     * @return Response
     * @Route("/destinationparRegion/{id}", name="listeDPRfront")
     */
    public function affcat(DestinationRepository $rep, $id)
    {
      
        $destinationfront=$rep->findAll();
        return $this->render('destinationFront/destinationparRegion.html.twig', [
            'destinationfront' => $destinationfront,
            'destination' => $rep->getDestinationsByRegionID($id),

            
        ]);

    }
   //jason
    /**
     * @Route("/afficherDestinations" , name="afficherDestinations")
     */
    public function afficherDestinationJson(DestinationRepository $rep, SerializerInterface $serializer): Response
    {
       $Destinations=$rep->findAll();
        //$json = $serializer->serialize($reclamations,'json',['groups'=>'reclamations']);
        //dump($json);
        //die;
        //return new Response(json_encode($json));

        $encoders = [ new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $json=$serializer->serialize($Destinations, 'json',['circular_reference_handler'=>function ($object){return $object->getId();
        }
        ]);

        $response=new Response($json);
        $response->headers->set('Content-Type','application/json');
        return $response;
    }

    /**
     * @param DestinationRepository $rep
     * @param SerializerInterface $serializer
     * @Route("/ajouterDestination" , name="ajouterDestinationJSON")
     */
    public function ajouterDestinationJson(Request $request, DestinationRepository $rep, SerializerInterface $serializer,NormalizerInterface $normalizer):Response
    {
        $destination= new Destination();
        $destination->setNom($request->get('nom'));
        //$reclamation->setUtilisateur($request->get('utilisateurID'));
      //  $destination->setUtilisateur($repU->find($request->get('utilisateurID')));
        $destination->setDescription($request->get('description'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($destination);
        $em->flush();
        $encoders= [new JsonEncoder()];
        $normalizers=[new ObjectNormalizer()];
        $serializer =new Serializer($normalizers,$encoders);
        $json=$normalizer->normalize($destination,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));

    }

    /**
     * @param Request $request
     * @param DestinationRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalize
     * @return Response
     * @throws ExceptionInterface
     * @Route("/modifierDestinaation", name="modifierdestinationJson")
     */
    public function modifierdestinationJson(Request $request,DestinationRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer):Response
    {
        $destination = $rep->find($request->get('id'));
        $destination->setNom($request->get('nom'));
       // $reclamation->setUtilisateur($repU->find($request->get('utilisateurID')));

        $destination->setDescription($request->get('description'));
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $json=$normalizer->normalize($destination,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

    /**
     * @param Request $request
     * @param DestinationRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws ExceptionInterface
     * @Route("/deleteDestination",name="deleteDestinationJson")
     */
    public function deleteDestinationJson(Request $request,DestinationRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer)
    {
        $destination = $rep->find($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($destination);
        $em->flush();
        $json=$normalizer->normalize($destination,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

}
