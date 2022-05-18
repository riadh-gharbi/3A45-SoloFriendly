<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie", name="categorie")
     */
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }

    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/categorie/add", name="categorie_add")
     */
    public function addCat(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
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
                $categorie->setPicture($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();
            return $this->redirectToRoute('categorie_show');
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param CategorieRepository $rep
     * @return Response
     * @Route("/categorie/show", name="categorie_show")
     */
    public function affcat(CategorieRepository $rep, Request $request,PaginatorInterface $paginator)
    {
        $categorie=$rep->findAll();
        $categorie = $paginator->paginate(
            $categorie,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 5)/*limit per page*/
        );
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /**
     * @param $id
     * @param CategorieRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/categorie/update/{id}", name="categorie_edit")
     */
    public function edit($id,CategorieRepository $rep,Request $request)
    {
        $categorie=$rep->find($id);
        $form = $this->createForm(CategorieType::class, $categorie);
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
                $categorie->setPicture($newFilename);
            }
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('categorie_show');
        }

        return $this->render('categorie/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param CategorieRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/categorie/delete/{id}", name="categorie_delete")
     */
    public function suppcat($id,CategorieRepository $rep)
    {
        $categorie=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();


        return $this->redirectToRoute('categorie_show');
    }

    /**
     * @Route("/afficherCategories" , name="afficherCategoriesJson")
     */
    public function afficherCategoriesJson(CategorieRepository $rep, SerializerInterface $serializer): Response
    {
        $categoeies=$rep->findAll();
        $categorieList =[];
        foreach ($categoeies as $categorie ){
            $categorieList[] = [
                'id' => $categorie->getId(),
                'nom' => $categorie->getNom(),
                'image' => $categorie->getImage()
            ];

        }
        return new Response(json_encode($categorieList));

        $encoders = [ new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $json=$serializer->serialize($categoeie, 'json',['circular_reference_handler'=>function ($object){return $object->getId();
        }
        ]);
        $response=new Response($json);
        $response->headers->set('Content-Type','application/json');
        return $response;
    }

    /**
     * @param CategorieRepository $rep
     * @param SerializerInterface $serializer
     * @Route("/add" , name="ajouterCatJSON")
     */
    public function ajouterCategoriesJson(Request $request, CategorieRepository $rep, SerializerInterface $serializer,NormalizerInterface $normalizer):Response
    {
        $categorie= new Categorie();
        $categorie->setNom($request->get('nom'));
        $image = $request->files->get('image');

        if ($image) {
            $newFilename = uniqid().'.'.$image->guessExtension();

            try {
                $image->move(
                    $this->getParameter('event_picture'),
                    $newFilename
                );
            } catch (FileException $e) {}
            $categorie->setPicture($newFilename);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($categorie);
        $em->flush();
        $encoders= [new JsonEncoder()];
        $normalizers=[new ObjectNormalizer()];
        $serializer =new Serializer($normalizers,$encoders);
        $json=$normalizer->normalize($categorie,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));

    }

    /**
     * @param Request $request
     * @param CategorieRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws ExceptionInterface
     * @Route("/modifierCategories", name="modifierCategoriesJson")
     */
    public function modifierCategoriesJson(Request $request,CategorieRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer):Response
    {
        $categorie = $rep->find($request->get('id'));
        $categorie->setNom($request->get('nom'));
       // $image = $request->get('image')->getData();

        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $json=$normalizer->normalize($categorie,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

    /**
     * @param Request $request
     * @param CategorieRepository $rep
     * @param SerializerInterface $serializer
     * @param NormalizerInterface $normalizer
     * @return Response
     * @throws ExceptionInterface
     * @Route("/deleteCategories",name="deleteCategoriesJson")
     */
    public function deleteCategoriesJson(Request $request,CategorieRepository $rep,SerializerInterface $serializer,NormalizerInterface $normalizer)
    {
        $categorie = $rep->find($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($categorie);
        $em->flush();
        $json=$normalizer->normalize($categorie,'json',['groups'=>'post:read']);
        return new Response(json_encode($json));
    }

}
