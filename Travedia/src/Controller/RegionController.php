<?php

namespace App\Controller;
use App\Entity\Region;
use App\Form\RegionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\RegionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


class RegionController extends AbstractController
{
    /**
     * @Route("/region", name="region")
     */
    public function index(): Response
    {
        return $this->render('region/index.html.twig', [
            'controller_name' => 'RegionController',
        ]);
    }
     /**
     * @Route("/regionadd", name="regionadd")
     */
    public function add(Request $request): Response
    {
        $region = new Region();

        $form = $this->createForm(RegionType::class,$region);
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
                $region->setImage($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($region);
          //  ->add('save', SubmitType::class, array( 'label' => 'Créer' ))->getForm();

            $entityManager->flush();
            $this->addFlash('success', 'region a été créé');

            return $this->redirectToRoute('listegion');

        }

        return $this->render('region/addRegion.html.twig',['form' => $form->createView()]);
    }



  /**
     * @Route("/regionliste", name="listegion")
     */
    public function liste(Request $request,PaginatorInterface $paginator )
          {
            $region= $this->getDoctrine()->getRepository(Region::class)->findAll();
            $region = $paginator->paginate(
              $region,  
              $request->query->getInt('page', 1)/*page number*/,
              $request->query->getInt('limit', 5)/*limit per page*/   
         );
            return $this->render('region/index.html.twig',['region'=> $region]);
            }

            
 /** 
  * @Route("/regionshow/{id}", name="regionshow") 
  */ 
 public function show($id) 
 { $region = $this->getDoctrine()->getRepository(Region::class) ->find($id); 
    return $this->render('region/showRegion.html.twig', ['region' => $region]);
 }


   
     /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/regionedit/{id}", name="regionedit")
     */
    public function edit($id,RegionRepository $rep,Request $request)
    {
        $region=$rep->find($id);
        $form = $this->createForm(RegionType::class, $region);
      //  ->add('save', SubmitType::class, array( 'label' => 'Modifier' ))->getForm();
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
                $region->setImage($newFilename);
            }
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('info', 'region a été modifié');

            return $this->redirectToRoute('listegion');
        }

        return $this->render('region/editRegion.html.twig', [
            'region'=>$region,
            'form' => $form->createView(),
        ]);
    }

// /** 
//  * @Route("/regiondelete/{id}", name="regionelete") * @Method({"DELETE"})
//  */
//  public function delete(Request $request, $id) 
//  { 
//      $region = $this->getDoctrine()->getRepository(Region::class)->findOneById($id); 
//      $entityManager = $this->getDoctrine()->getManager();
//       $entityManager->remove($region);
//        $entityManager->flush();
//          return $this->redirectToRoute('listegion'); 
//  }
 /**
     * @param $id
     * @param RegionRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/regiondelete/{id}", name="regionelete")
     */
    public function suppcat($id,RegionRepository $rep)
    {
        $region=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($region);
        $entityManager->flush();

        $this->addFlash('error', 'region a été supprimé');

        return $this->redirectToRoute('listegion');
    }
    //jason
    /**
     * @Route("/afficherRegion" , name="afficherregionjson")
     */
    public function afficherRegionJson(RegionRepository $rep, SerializerInterface $serializer): Response
    {
        $Regions=$rep->findAll();
        //$json = $serializer->serialize($reclamations,'json',['groups'=>'reclamations']);
        //dump($json);
        //die;
        //return new Response(json_encode($json));

        $encoders = [ new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $json=$serializer->serialize($Regions, 'json',['circular_reference_handler'=>function ($object){return $object->getId();
        }
        ]);

        $response=new Response($json);
        $response->headers->set('Content-Type','application/json');
        return $response;
    }

    /**
     * @param RegionRepository $rep
     * @param SerializerInterface $serializer
     * @Route("/ajouterRegionn" , name="ajouterRegionJSON")
     */
    public function ajouterregionJson(Request $request, RegionRepository $rep, SerializerInterface $serializer,NormalizerInterface $normalizer):Response
    {
        $region= new Region();
        $region->setNom($request->get('nom'));
        $image = $request->files->get('image');
        if ($image) {
            $newFilename = uniqid().'.'.$image->guessExtension();

            try {
                $image->move(
                    $this->getParameter('brochures_directory'),
                    $newFilename
                );
            } catch (FileException $e) {}
            $region->setImage($newFilename);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($region);
        $em->flush();
        $encoders= [new JsonEncoder()];
        $normalizers=[new ObjectNormalizer()];
        $serializer =new Serializer($normalizers,$encoders);
        $json=$normalizer->normalize($region,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));

    }

    /**
     * @param Request $request
     * @param RegionRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalize
     * @return Response
     * @throws ExceptionInterface
     * @Route("/modifierregionn", name="modifierregionJson")
     */
    public function modifierregionJson(Request $request,RegionRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer):Response
    {
        $region = $rep->find($request->get('id'));
        $region->setNom($request->get('nom'));
        // $reclamation->setUtilisateur($repU->find($request->get('utilisateurID')));

        //   $region->setDescription($request->get('description'));
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $json=$normalizer->normalize($region,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

    /**
     * @param Request $request
     * @param RegionRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws ExceptionInterface
     * @Route("/deleteRegions",name="deleteregionJson")
     */
    public function deleteregionJson(Request $request,RegionRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer)
    {
        $region = $rep->find($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($region);
        $em->flush();
        $json=$normalizer->normalize($region,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }
    /**
     * @Route("/regionid/{id}",name="regionid")
     */
    public function regionid($id)
    {
        $reclamation=$this->getDoctrine()->getRepository(Region::class)->affregionn($id);
        //$regions=$repository->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reclamation);

        return new JsonResponse($formatted);

    }
}
