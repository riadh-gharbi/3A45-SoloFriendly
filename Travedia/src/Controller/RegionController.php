<?php

namespace App\Controller;
use App\Entity\Region;
use App\Form\RegionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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


        return $this->redirectToRoute('listegion');
    }

}
