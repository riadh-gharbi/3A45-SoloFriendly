<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Profile;
use App\Form\ProfileFormType;
use App\Form\RegistrationFormType;
use App\Repository\ProfileRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;


class UtilisateurController extends AbstractController
{

    public function __construct(VerifyEmailHelperInterface $helper, Swift_Mailer $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }

    //CODENAME ONE//
    /**
     * @Route("/user/signUp", name="signup_mobile")
     */
    public function signupMobile(Request $request, UserPasswordEncoderInterface $PasswordEncoder, Swift_Mailer $mailer)
    {
        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        //$cin= $request->query->get("cin");
        $prenom = $request->query->get("prenom");
        // $adresse= $request->query->get("adresse");
        $roles = $request->query->get("roles");
        $langue = $request->query->get("langue");
        $password = $request->query->get("password");

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Response("email invalid");
        }


        $user = new Utilisateur();
        $profile = new Profile();
        $user->setNom($nom);
        $user->setEmail($email);
        $profile->setUtilisateur($user);
        $user->setPassword(
            $PasswordEncoder->encodePassword($user, $password));
        $user->setIsVerified(false);
        $user->setPrenom($prenom);
        $user->setLangue($langue);
        $user->setRoles(array($roles));

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($profile);
            $em->flush();
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'registration_confirmation_route',
                $user->getId(),
                $user->getEmail()
            );
            $message = (new \Swift_Message('Vérifier Votre compte'))
                ->setFrom('travediacontact@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('emails/registration.html.twig', [
                            'signedUrl' => $signatureComponents->getSignedUrl()
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);


            return new JsonResponse("Bienvenue à Travedia", 200);

        } catch (\Exception $ex) {
            return new Response("erreur de creation du compte" );
        }
    }

    /**
     * @Route("/user/signin", name="signin_mobile")
     */

    public function signinMobile(Request $request)
    {
        $email = $request->query->get("email");
        $password = $request->query->get("password");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        if ($user) {
            if (password_verify($password, $user->getPassword())) {

                /*             $serializer = new Serializer([new ObjectNormalizer()]);
                             $formatted = $serializer->normalize($user);
                             return new JsonResponse($formatted);*/


                $encoders = [new JsonEncoder()];
                $normalizers = [new ObjectNormalizer()];
                $serializer = new Serializer($normalizers, $encoders);
                $json = $serializer->serialize($user, 'json', ['circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
                ]);

                $response = new Response($json);
                $response->headers->set('Content-Type', 'application/json;charset=UTF-8');
                return $response;
            } else {

                return new Response("password not found",500);
            }
        } else {
            return new Response("user not found",500);
        }
    }


    /**
     * @Route("/user/editUserMobile", name="edituser_mobile")
     */
    public function editUserMobile(Request $request, UserPasswordEncoderInterface $PasswordEncoder):Response
    {

        $id = $request->get("id");

        //hethi string
        $profileId = $request->query->get("profileId");
        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        //$cin = $request->query->get("cin");
        $prenom = $request->query->get("prenom");
        //$numTel = $request->query->get("numTel");
        //$adresse = $request->query->get("adresse");
        $roles = $request->query->get("roles");
        $langue = $request->query->get("langue");
        $password = $request->query->get("password");
        $description = $request->query->get("description");
        //$image = $request->query->get("image");
        $evaluation = $request->query->get("evaluation");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Utilisateur::class)->find($id);
        $profile = $this->getDoctrine()->getRepository(Profile::class)->find($profileId);

        if ($request->files->get("photo") != null) {

            $file = $request->files->get("photo");
            $fileName = $file->getClientOriginalName();

            $file->move($fileName);
            $user->setPhoto($fileName);
        }
        //set profile lezmou object de type profile mouch string
        $profile->setDescription($description);
        $profile->setEvaluation($evaluation);
        //$profile->setImage($image);
        $user->setProfile($profile);
        $user->setNom($nom);
        $user->setEmail($email);
        $user->setPassword(
            $PasswordEncoder->encodePassword(
                $user,
                $password));
        $user->setIsVerified(true);
        //$user->setNumTel($numTel);
        //$user->setCin($cin);
        $user->setPrenom($prenom);
        //$user->setAdresse($adresse);
        $user->setLangue($langue);
        $user->setRoles(array($roles));

        try {
            $em = $this->getDoctrine()->getManager();
            //$em->persist($profile);
            //$em->persist($user);
            $em->flush();

            return new JsonResponse("Account Modified", 200);

        } catch (\Exception $ex) {
            return new Response("Failed" . $ex->getMessage(),500);
        }
    }

    /**
     * @Route("/mobile/user/delete", name="user_mobile_delete")
     */
    public function deleteUserMobile(Request $request): Response
    {
        $id = $request->query->get('id');
        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['id' => $id]);
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            return new JsonResponse("utilisateur supprimé");
        } catch (\Exception $e) {
            return new JsonResponse("erreur " . $e->getMessage());
        }
    }


    /**
     * @Route("/error", name="error")
     */
    public function error()
    {
        return $this->render('utilisateur/error.html.twig');
    }

    /**
     * @Route("/user/profile", name="user_profile")
     */
    public function getUserProfile(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($user->getIsBlocked() == true) {
            return $this->redirectToRoute('error');
        } else {

            if ($user->getRoles() == ["ROLE_ADMIN"]) {
                return $this->redirectToRoute('admin_profile');
            } else {
                return $this->render('utilisateur/userProfile.html.twig', [
                    'utilisateur' => $user,
                ]);
            }
        }
    }

    /**
     * @Route("/user/delete", name="user_delete")
     */
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
        }
        if ($user->getIsBlocked() == true) {
            return $this->redirectToRoute('error');
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
     * @Route("/admin/block/{id}",name="block_user")
     */
    public function blockUser($id): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }
        if ($currentUser->getRoles() == ["ROLE_ADMIN"]) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getDoctrine()->getRepository(Utilisateur::class)->find($id);
            $user->setIsBlocked(true);
            $em->flush();

            return $this->redirectToRoute('all_users');
        } else {
            return $this->redirectToRoute('error');
        }
    }

    /**
     * @Route("/admin/unblock/{id}",name="unblock_user")
     */
    public function unblockUser($id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($user->getRoles() == ["ROLE_ADMIN"]) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getDoctrine()->getRepository(Utilisateur::class)->find($id);
            $user->setIsBlocked(false);
            $em->flush();

            return $this->redirectToRoute('all_users');
        } else {
            return $this->redirectToRoute('error');
        }
    }

    /**
     * @Route("/admin/profile", name="admin_profile")
     */
    public function getAdminProfile(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($user->getRoles() != ["ROLE_ADMIN"]) {
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
        if ($user->getRoles() != ["ROLE_ADMIN"]) {
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
                ->add('langue')
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
     * @Route("/admin/delete/{id}", name="delete_user")
     */
    public function deleteUser(UtilisateurRepository $repo, $id)
    {
        $user = $repo->find($id);
        if (!$user) {
            return $this->redirectToRoute('app_login');
        } else {
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            return $this->redirectToRoute('all_users');
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
        if ($user->getRoles() == ["ROLE_ADMIN"]) {
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
            return $this->redirectToRoute('error');
        }
    }

    /**
     * @Route("/admin/add", name="add_account")
     */
    public function addAccount(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager)
    {
        $user = new Utilisateur();
        $profile = new Profile();
        $form = $this->createForm(RegistrationFormType::class, $user)
            ->add('cin', NumberType::class)
            ->add('numTel', NumberType::class)
            ->add('adresse', TextType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIsVerified(true);

            $profile->setUtilisateur($user);
            $entityManager->persist($user);
            $entityManager->persist($profile);
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
            ->add('cin', NumberType::class)
            ->add('numTel', NumberType::class)
            ->add('adresse', TextType::class);
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
     * @Route("/admin/stats", name="admin_stats")
     */
    public function stats(UtilisateurRepository $userRepo)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        } else if ($user->getRoles() == ["ROLE_ADMIN"]) {
            $data = $userRepo->findAll();
            $arabe = 0;
            $francais = 0;
            $anglais = 0;
            $admin = 0;
            $guide = 0;
            $voyageur = 0;
            foreach ($data as $t) {
                $type = $t->getRoles();
                if ($type == ["ROLE_ADMIN"]) {
                    $admin++;
                } elseif ($type == ["ROLE_Guide"]) {
                    $guide++;
                } elseif ($type == ["ROLE_Voyageur"]) {
                    $voyageur++;
                }

                if ($t->getLangue() == "Arabe") {
                    $arabe++;
                } elseif ($t->getLangue() == "Français") {
                    $francais++;
                }
                elseif($t->getLangue() == "Anglais")
                {
                    $anglais++;
                }
            }

            $choice = ['Admin', 'Guide', 'Voyageur'];

            return $this->render('utilisateur/stat.html.twig',
                [
                    'admin' => $admin,
                    'guide' => $guide,
                    'voyageur' => $voyageur,
                    'choice' => json_encode($choice),
                    'etat' => [$arabe, $francais,$anglais],
                ]
            );
        } else {
            return $this->redirectToRoute('error');
        }
    }

}
