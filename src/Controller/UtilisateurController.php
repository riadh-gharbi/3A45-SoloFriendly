<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ProfileFormType;
use App\Form\RegistrationFormType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/user/profile", name="user_profile")
     */
    //TODO: complete function
    public function getUserProfile(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($user->getRoles() == ["ADMIN"]) {
            return $this->redirectToRoute('admin_profile');
        } else {
            return $this->render('utilisateur/userProfile.html.twig', [
                'utilisateur' => $user,
            ]);
        }
    }

    /**
     * @Route("/user/delete", name="user_delete")
     */
    //TODO: verify before deleting account
    public function deleteCurrentUser(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        } else {
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            return $this->redirectToRoute('app_logout');
        }
    }

    /**
     * @Route("/user/edit", name="edit_user")
     */
    public function editUserAccount(Request $req, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        } else {
            $profile = $user->getProfile();
            $em = $this->getDoctrine()->getManager();
            $form = $this->createFormBuilder($user)
                ->add('cin')
                ->add('nom')
                ->add('prenom')
                ->add('numTel')
                ->add('adresse')
                ->add('langue')
                ->add('email')
                ->add('old_password', PasswordType::class, [
                    'mapped' => false,
                    'label' => false,
                    'required' => false,
                ])
                ->add('new_password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'required' => false,
                    'first_options' => [
                        'label' => false,
                    ],
                    'second_options' => [
                        'label' => false,
                    ]
                ])
                ->add('profile', ProfileFormType::class)
                ->getForm();
            $form->handleRequest($req);
            $profilePicture = $form->get('profile')->get('image')->getData();
            if ($form->isSubmitted() && $form->isValid()) {

                if ($profilePicture) {
                    $newFilename = uniqid() . '.' . $profilePicture->guessExtension();
                    try {
                        $profilePicture->move(
                            $this->getParameter('profile_image'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }
                    $profile->setImage($newFilename);
                }

                $old_password = $form->get('old_password')->getData();
                if ($encoder->isPasswordValid($user, $old_password)) {
                    $new_password = $form->get('new_password')->getData();
                    $password = $encoder->encodePassword($user, $new_password);
                    $user->setPassword($password);
                    $em->flush();
                    return $this->redirectToRoute('user_profile');
                }
            }
            return $this->render('utilisateur/editProfile.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/admin/profile", name="admin_profile")
     */
    //TODO: complete function
    public function getAdminProfile(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($user->getRoles() != ["ADMIN"]) {
            return $this->redirectToRoute('user_profile');
        } else {
            return $this->render('utilisateur/adminProfile.html.twig', [
                'utilisateur' => $user,
            ]);
        }
    }

    /**
     * @Route("/admin/edit", name="edit_admin")
     */
    public function editAdminAccount(Request $req, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($user->getRoles() != ["ADMIN"]) {
            return $this->redirectToRoute('home');
        } else {
            $profile = $user->getProfile();
            $em = $this->getDoctrine()->getManager();
            $form = $this->createFormBuilder($user)
                ->add('cin')
                ->add('nom')
                ->add('prenom')
                ->add('numTel')
                ->add('adresse')
                ->add('email')
                ->add('old_password', PasswordType::class, [
                    'mapped' => false,
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'old password'
                    ]
                ])
                ->add('new_password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'required' => false,
                    'first_options' => [
                        'label' => false,
                    ],
                    'second_options' => [
                        'label' => false,
                    ]
                ])
                ->add('profile', ProfileFormType::class)
                ->getForm();
            $form->handleRequest($req);
            $profilePicture = $form->get('profile')->get('image')->getData();
            if ($form->isSubmitted() && $form->isValid()) {

                if ($profilePicture) {
                    $newFilename = uniqid() . '.' . $profilePicture->guessExtension();
                    try {
                        $profilePicture->move(
                            $this->getParameter('profile_image'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }
                    $profile->setImage($newFilename);
                }

                $old_password = $form->get('old_password')->getData();
                if ($encoder->isPasswordValid($user, $old_password)) {
                    $new_password = $form->get('new_password')->getData();
                    $password = $encoder->encodePassword($user, $new_password);
                    $user->setPassword($password);
                    $em->flush();
                    return $this->redirectToRoute('user_profile');
                }
            }
            return $this->render('utilisateur/editProfile.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/admin/delete", name="delete_user")
     */
    public function deleteUser(UtilisateurRepository $repo)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        } else {
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            return $this->redirectToRoute('app_logout');
        }
    }

    /**
     * @Route("/admin/allUsers",name="all_users")
     */
    public function getAllUsers(UtilisateurRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($user->getRoles() == ["ADMIN"]) {
            $qbUser = $repo->createQueryBuilder('utilisateur')->addSelect('utilisateur');
            $qbProfile = $repo->createQueryBuilder('utilisateur')->addSelect('utilisateur');
            $userPagination = $paginator->paginate(
                $qbUser, /* query NOT result */
                $request->query->getInt('page1', 1), /*page number*/
                3,
                array(
                    'pageParameterName' => 'page1',
                    'sortFieldParameterName' => 'sort1',
                    'sortDirectionParameterName' => 'direction1',
                ) /*limit per page*/
            );
            $profilePagination = $paginator->paginate(
                $qbProfile, /* query NOT result */
                $request->query->getInt('page2', 1), /*page number*/
                3,
                array(
                    'pageParameterName' => 'page2',
                    'sortFieldParameterName' => 'sort2',
                    'sortDirectionParameterName' => 'direction2',
                )
            );

            return $this->render('utilisateur/allUsers.html.twig', [
                'utilisateur' => $userPagination,
                'profile' => $profilePagination
            ]);
        } else {
            //TODO:GENERATE A 404 PAGE
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/admin/add", name="add_account")
     */
    public function addAccount(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager)
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user)
        ->add('cin',NumberType::class)
        ->add('numTel', NumberType::class)
        ->add('adresse',TextType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('all_users');
        }

        return $this->render('registration/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/editUser/{id}", name="edit_account")
     */
    public function editUserAccountByAdmin(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, $id, UtilisateurRepository $repo)
    {
        $user = $repo->find($id);
        $form = $this->createForm(RegistrationFormType::class, $user)
        ->add('cin',NumberType::class)
        ->add('numTel', NumberType::class)
        ->add('adresse',TextType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('all_users');
        }

        return $this->render('registration/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
