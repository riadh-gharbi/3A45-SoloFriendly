<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Form\HotelType;
use App\Repository\HotelRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;

class HotelController extends AbstractController
{
    /**
     * @Route("/hotel", name="hotel")
     */
    public function index(): Response
    {
        return $this->render('hotel/index.html.twig', [
            'controller_name' => 'HotelController',
        ]);
    }

    /**
     * @param Request $request
     * @param HotelRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route ("/hotel/add",name="hotel_add")
     */
    public function addHotel(Request $request): Response
    {

        $hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hotel);
            $entityManager->flush();
            return $this->redirectToRoute('hotel_show');
        }
        return $this->render('hotel/addHotel.html.twig', [
            'hotel' => $hotel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param HotelRepository $rep
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * * @Route("/hotel/show/",name="hotel_show")
     */
    public function showHotel(HotelRepository  $rep,Request $request,PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $HotelRepository = $em->getRepository(Hotel::class);
        $allHotelQuery = $HotelRepository->createQueryBuilder('p')
            ->where('p.id != :id')
            ->setParameter('id', 'canceled')
            ->getQuery();



        $hotel = $paginator->paginate(
            $allHotelQuery,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('hotel/showHotel.html.twig', [
            'hotel' => $hotel,
        ]);
    }

    /**
     * @param $id
     * @param HotelRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/hotel/edit/{id}",name="hotel_edit")
     */
    public function editHotel($id,HotelRepository $rep,Request $request)
    {
        $hotel=$rep->find($id);
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('hotel_show');
        }

        return $this->render('hotel/editHotel.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param HotelRepository $rep
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/hotel/del/{id}",name="hotel_del")
     */
    public function delHotel($id,HotelRepository $rep)
    {
        $hotel=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($hotel);
        $entityManager->flush();


        return $this->redirectToRoute('hotel_show');
    }

    /**
     * @param HotelRepository $rep
     * @param SerializerInterface $serializer
     * @Route("/afficherHotel" , name="afficherHotelsjson")
     */
    public function afficherHotelJson(HotelRepository $rep, SerializerInterface $serializer): Response
    {
        $hotels=$rep->findAll();
        $encoders = [ new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $json=$serializer->serialize($hotels, 'json',['circular_reference_handler'=>function ($object){return $object->getId();
        }
        ]);

        $response=new Response($json);
        $response->headers->set('Content-Type','application/json');
        return $response;
    }
    /**
     * @param HotelRepository $rep
     * @param SerializerInterface $serializer
     * @Route("/ajouterHotel" , name="ajouterHotelJSON")
     */
    public function ajouterHotelJson(Request $request,HotelRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer):Response
    {
        $hotels= new Hotel();
        $hotels->setNom($request->get('Nom'));
        $hotels->setAdresse($request->get('adresse'));
        $hotels->setEmail($request->get('Email'));
        $hotels->setNumTel($request->get('NumTel'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($hotels);
        $em->flush();
        $encoders= [new JsonEncoder()];
        $normalizers=[new ObjectNormalizer()];
        $serializer =new Serializer($normalizers,$encoders);
        $json=$normalizer->normalize($hotels,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));

    }
    /**
     * @param Request $request
     * @param HotelRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws ExceptionInterface
     * @Route("/modifierHotel", name="modifierHotelJSON")
     */
    public function modifierHotelRequest( $request,HotelRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer):Response
    {
        $hotels = $rep->find($request->get('id'));
        $hotels->setNom($request->get('Nom'));
        $hotels->setAdresse($request->get('adresse'));
        $hotels->setEmail($request->get('Email'));
        $hotels->setNumTel($request->get('NumTel'));
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $json=$normalizer->normalize($hotels,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

    /**
     * @param Request $request
     * @param HotelRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws ExceptionInterface
     * @Route("/deleteHotel",name="deleteHotelJson")
     */
    public function deleteHotelJson(Request $request,HotelRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer)
    {
        $hotels = $rep->find($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($hotels);
        $em->flush();
        $json=$normalizer->normalize($hotels,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }
}
