<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\Utilisateur;
use App\Form\AdminRegistrationFormType;
use App\Form\RegistrationFormType;
use App\Security\UserAuthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/user/register", name="user_register")
     */
    public function register(Request $request, \Swift_Mailer $mailer, UserPasswordEncoderInterface $userPasswordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $profile = new Profile();
        $form = $this->createForm(RegistrationFormType::class, $user)
            ->add('captcha',CaptchaType::class,array(
                'width' => 200,
                'height' => 50,
                'length' => 6,
                'label' => false,
                'quality' => 90,
                'distortion' => true,
                'background_color' => [115, 194, 251],
                'max_front_lines' => 0,
                'max_behind_lines' => 0,
                'attr' => array('class' => 'form-control',
                    'rows'=> "6"
                )
            ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $profile->setUtilisateur($user);
            $entityManager->persist($user);
            $entityManager->persist($profile);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $message = (new \Swift_Message('test'))
                ->setFrom('travediacontact@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/registration.html.twig'
                    ),
                    'text/html'
                );
            $mailer->send($message);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/register", name="admin_register")
     */
    public function registerAdmin(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $profile = new Profile();
        $form = $this->createForm(AdminRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(["ADMIN"]);
            $profile->setUtilisateur($user);
            $entityManager->persist($user);
            $entityManager->persist($profile);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/registerAdmin.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
