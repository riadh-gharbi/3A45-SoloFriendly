<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Evenement;
use App\Form\CategorieType;
use App\Form\EvenementType;
use App\Repository\CategorieRepository;
use App\Repository\CatRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;

class EvenementController extends AbstractController
{
    /**
     * @Route("/evenement", name="evenement")
     */
    public function index(): Response
    {
        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
        ]);
    }

    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/evenement/add", name="evenement_add")
     */
    public function addEv(Request $request): Response
    {
        $evenment = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenment);
        $form->handleRequest($request);
//winou code limage houni
        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->get('picture')->getData();

            if ($picture) {
                $newFilename = uniqid().'.'.$picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('event_picture'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $evenment->setPicture($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenment);
            $entityManager->flush();
            return $this->redirectToRoute('evenement_show');
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param EvenementRepository $rep
     * @return Response
     * @Route("/evenement/show", name="evenement_show")
     */
    public function affcat(EvenementRepository $rep,Request $request,PaginatorInterface $paginator)
    {
        $evenement=$rep->findAll();
        $evenement = $paginator->paginate(
            $evenement,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 5)/*limit per page*/
        );
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    /**
     * @param $id
     * @param EvenementRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/evenement/update/{id}", name="evenement_edit")
     */
    public function edit($id,EvenementRepository $rep,Request $request)
    {
        $evenement=$rep->find($id);
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->remove('picture');
        $form->add('picture', FileType::class, [
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'image/*',
                    ],
                    'mimeTypesMessage' => 'Verify your image type',
                ])
            ],
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->get('picture')->getData();
            if ($picture) {
                $newFilename = uniqid().'.'.$picture->guessExtension();

                try {
                    $picture->move(
                        $this->getParameter('event_picture'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $evenement->setPicture($newFilename);
            }
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('evenement_show');
        }

        return $this->render('evenement/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param EvenementRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/evenement/delete/{id}", name="evenement_delete")
     */
    public function suppcat($id,EvenementRepository $rep)
    {
        $evenement=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($evenement);
        $entityManager->flush();


        return $this->redirectToRoute('evenement_show');
    }

    /**
     * @Route("/afficherEvenements" , name="afficherEvenementsJson")
     */
    public function afficherEvenementsJson(EvenementRepository $rep, SerializerInterface $serializer): Response
    {
        $evenements=$rep->findAll();
        $eventsList = [];

        foreach($evenements as $evenement){
            $eventsList[] = [
                'id' => $evenement->getId(),
                'nom' => $evenement->getNom(),
                'description' => $evenement->getDescription(),
                'datedeb' => $evenement->getDatedeb()->format("y-m-d"),
                'datefin' => $evenement->getDatefin()->format("y-m-d"),
                'categorie' => $evenement->getCategorie()==null?"nothing":$evenement->getCategorie()->getNom(),
                'image' => $evenement->getImage()
            ];
        }

        return new Response(json_encode($eventsList));

        $evenement=$rep->findAll();
        $encoders = [ new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $json=$serializer->serialize($evenement, 'json',['circular_reference_handler'=>function ($object){return $object->getId();
        }
        ]);
        $response=new Response($json);
        $response->headers->set('Content-Type','application/json');
        return $response;
    }

    /**
     * @param EvenementRepository $rep
     * @param SerializerInterface $serializer
     * @Route("/addev" , name="ajouterEvJSON")
     */
    public function ajouterEvenementsJson(Request $request, CategorieRepository $CR, EvenementRepository $rep, SerializerInterface $serializer,NormalizerInterface $normalizer):Response
    {
        $evenement= new Evenement();
        $evenement->setNom($request->get('nom'));
        $image = $request->files->get("image");
        if ($image) {
            $newFilename = uniqid().'.'.$image->guessExtension();

            try {
                $image->move(
                    $this->getParameter('event_picture'),
                    $newFilename
                );
            } catch (FileException $e) {}
            $evenement->setImage($newFilename);
        }
        $evenement->setDescription($request->get('description'));
        $evenement->setDatedeb(new \DateTime($request->get('datedeb')));
        $evenement->setDatefin(new \DateTime($request->get('datefin')));
        $evenement->setCategorie($CR->find($request->get('categorie')));
        $em = $this->getDoctrine()->getManager();
        $em->persist($evenement);
        $em->flush();
        $encoders= [new JsonEncoder()];
        $normalizers=[new ObjectNormalizer()];
        $serializer =new Serializer($normalizers,$encoders);
        $json=$normalizer->normalize($evenement,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));

    }

    /**
     * @param Request $request
     * @param EvenementRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws ExceptionInterface
     * @Route("/modifierEvenements", name="modifierEvenementsJson")
     */
    public function modifierEvenementsJson(Request $request,EvenementRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer):Response
    {
        $evenement = $rep->find($request->get('id'));
        $evenement->setNom($request->get('nom'));

        $evenement->setDescription($request->get('description'));

        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $json=$normalizer->normalize($evenement,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

    /**
     * @param Request $request
     * @param EvenementRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws ExceptionInterface
     * @Route("/deleteEvenements",name="deleteEvenementsJson")
     */
    public function deleteEvenementesJson(Request $request,EvenementRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer)
    {
        $evenement = $rep->find($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($evenement);
        $em->flush();
        $json=$normalizer->normalize($evenement,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

}
