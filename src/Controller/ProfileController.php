<?php

namespace App\Controller;




use Symfony\Component\Form\FormBuilderInterface;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    /**
     * @param ProfileRepository $rep
     * @return Response
     * @Route("/profile/admin/affiche", name="profile_list")
     */
    public function afficher(ProfileRepository $rep)
    {
        $profile = $rep->findAll();
        return $this->render('profile/admin/affiche.html.twig', [
            'profile' => $profile,
        ]);
    }



    /**
     * @param ProfileRepository $rep
     * @return Response
     * @Route("/profile/affiche", name="profile_list_front")
     */
    public function afficherf(ProfileRepository $rep)
    {
        $profile = $rep->findAll();
        return $this->render('profile/affiche.html.twig', [
            'profile' => $profile,
        ]);
    }




    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/profile/admin/ajout", name="profile_add")
     */
    public function add(Request $request): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->add('Creer', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('profile_image'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $profile->setImage($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profile);
            $entityManager->flush();
            return $this->redirectToRoute('profile_list');
        }

        return $this->render('profile/admin/ajout.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/profile/ajout", name="profile_add_front")
     */
    public function addf(Request $request): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->add('Creer', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('profile_image'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $profile->setImage($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profile);
            $entityManager->flush();
            return $this->redirectToRoute('profile_list_front');
        }

        return $this->render('profile/ajout.html.twig', [
            'profile' => $profile,
            'formfront' => $form->createView(),
        ]);
    }


    /**
     * @param $id
     * @param ProfileRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/profile/admin/edit/{id}", name="profile_edit")
     */
    public function edit($id, ProfileRepository $rep, Request $request)
    {
        $profile = $rep->find($id);
        $form = $this->createForm(ProfileType::class, $profile);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('profile_image'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $profile->setImage($newFilename);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('profile_list');

        }

        return $this->render('profile/admin/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param $id
     * @param ProfileRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/profile/edit/{id}", name="profile_edit_front")
     */
    public function editfr($id, ProfileRepository $rep, Request $request)
    {
        $profile = $rep->find($id);
        $form = $this->createForm(ProfileType::class, $profile);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('profile_image'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $profile->setImage($newFilename);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('profile_list_front');

        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }





    /**
     * @param $id
     * @param ProfileRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/profile/admin/delete/{id}", name="profile_delete")
     */
    public function supp($id, ProfileRepository $rep)
    {
        $profile = $rep->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($profile);
        $entityManager->flush();
        return $this->redirectToRoute('profile_list');
    }


    /**
     * @param $id
     * @param ProfileRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/profile/delete/{id}", name="profile_delete_front")
     */
    public function suppfr($id, ProfileRepository $rep)
    {
        $profile = $rep->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($profile);
        $entityManager->flush();


        return $this->redirectToRoute('profile_list_front');
    }




}

