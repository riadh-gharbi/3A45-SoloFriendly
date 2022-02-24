<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\Utilisateur;
use App\Form\SignupType;
use App\Form\UtilisateurType;
use App\Form\UserType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class UtilisateurController extends AbstractController
{
    /**
     * @Route("/utilisateur", name="utilisateur")
     */
    public function index(): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'controller_name' => 'UtilisateurController',
        ]);
    }


    /**
     * @param utilisateurRepository $rep
     * @return Response
     * @Route("/utilisateur/admin/table", name="user_list")
     */
    public function afficher(UtilisateurRepository $rep)
    {
        $utilisateur=$rep->findAll();
        return $this->render('utilisateur/admin/table.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }



    /**
     * @param utilisateurRepository $rep
     * @return Response
     * @Route("/utilisateur/table", name="user_list_front")
     */
    public function afficherfront(UtilisateurRepository $rep)
    {
        $utilisateur=$rep->findAll();
        return $this->render('utilisateur/table.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }


    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/utilisateur/admin/add", name="utilisateur_add")
     */
    public function add (Request $request): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            return $this->redirectToRoute('user_list');
        }

        return $this->render('utilisateur/admin/add.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @param Request $request
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/utilisateur/add", name="utilisateur_add_front")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $utilisateur = new Utilisateur();
        $profile = new Profile();
        $form = $this->createForm(SignupType::class, $utilisateur);
        $form->add('Create', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setMotDePasse(
                $passwordEncoder->encodePassword(
                    $utilisateur,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->persist($profile);
            $entityManager->flush();
            return $this->redirectToRoute($profile);
        }

        return $this->render('utilisateur/add.html.twig', [
            'utilisateur' => $utilisateur,
            'profile' => $profile,
            'formf'=>$form->createView(),
        ]);
    }



    /**
     * @param $id
     * @param UtilisateurRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/utilisateur/admin/update/{id}", name="user_edit")
     */
    public function edit($id,UtilisateurRepository $rep,Request $request)
    {
        $utilisateur=$rep->find($id);
        $form=$this ->createForm(UtilisateurType::class,$utilisateur);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {$em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('user_list');
        }
        return $this->render('utilisateur/admin/update.html.twig', [
            'form'=>$form->createView(),
        ]);
    }




    /**
     * @param $id
     * @param UtilisateurRepository $rep
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/utilisateur/update/{id}", name="user_edit_front")
     */
    public function editfront($id,UtilisateurRepository $rep,Request $request)
    {
        $utilisateur=$rep->find($id);
        $form=$this ->createForm(UserType::class,$utilisateur);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {$em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('user_list_front');

        }

        return $this->render('utilisateur/updatef.html.twig', [
            'form'=>$form->createView(),
        ]);
    }


    /**
     * @param $id
     * @param UtilisateurRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/utilisateur/admin/delete/{id}", name="user_delete")
     */
    public function supp($id,UtilisateurRepository $rep)
    {
        $utilisateur=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($utilisateur);
        $entityManager->flush();


        return $this->redirectToRoute('user_list');
    }




    /**
     * @param $id
     * @param UtilisateurRepository $rep
     * @return \symfony\Component_HttpFoundation\RedirectResponse
     * @Route("/utilisateur/delete/{id}", name="user_delete_front")
     */
    public function suppfront($id,UtilisateurRepository $rep)
    {
        $utilisateur=$rep->find($id);
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($utilisateur);
        $entityManager->flush();


        return $this->redirectToRoute('user_list_front');
    }


}
