<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Entity\Region;
use App\Entity\RegionType;

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

 



class DestinationController extends AbstractController
{
    /**
     * @Route("/destinationadd", name="destination")
     */
    public function add(Request $request): Response
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
          //  ->add('save', SubmitType::class, array( 'label' => 'Créer' ))->getForm();

            $entityManager->flush();

            $this->addFlash('success', 'destination a été créé');
            return $this->redirectToRoute('listeD');

        }

        return $this->render('destination/add.html.twig',['form' => $form->createView()]);
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
    public function liste(Request $request,PaginatorInterface $paginator )
          {
            $destination= $this->getDoctrine()->getRepository(Destination::class)->findAll();
            $destination = $paginator->paginate(
              $destination,  
              $request->query->getInt('page', 1)/*page number*/,
              $request->query->getInt('limit', 5)/*limit per page*/   
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
        $request->query->getInt('limit', 5)/*limit per page*/   
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
    // public function affcat(EvenementRepository $rep, $id)
    // {
    //     /*$evenement=$rep->findAll();
    //     return $this->render('evenement_f/show.html.twig', [
    //         'evenement' => $evenement,
    //     ]);*/
    //     $evenement=$rep->findAll();
    //     return $this->render('evenement_f/show.html.twig', [
    //         'evenement' => $evenement,
    //         'event' => $rep->getEventsByCategoryID($id),
    //     ]);

    // }

}
