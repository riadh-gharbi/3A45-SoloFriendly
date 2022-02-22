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
}
