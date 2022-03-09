<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="message")
     */
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }


    /**
     * @Route("/user/message/", name="add_message")
     */
    public function AjouterMessage(Request $request, EntityManagerInterface $entityManager, $id, UtilisateurRepository $repo)
    {
        $user = $repo->find($id);
        $form = $this->createForm(RegistrationFormType::class, $user)
            ->add('cin',NumberType::class)
            ->add('numTel', NumberType::class)
            ->add('adresse',TextType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password


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
     * @Route("/admin/allmessages",name="all_messages")
     */
    public function getAllMessages(UtilisateurRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($user->getRoles() == ["ADMIN"]) {
            $qbMessage = $repo->createQueryBuilder('message')->addSelect('message');
            $messagePagination = $paginator->paginate(
                $qbMessage, /* query NOT result */
                $request->query->getInt('page1', 1), /*page number*/
                3,
                array(
                    'pageParameterName' => 'page1',
                    'sortFieldParameterName' => 'sort1',
                    'sortDirectionParameterName' => 'direction1',
                ) /*limit per page*/
            );

            return $this->render('message/allmessages.html.twig', [
                'message' => $messagePagination,
            ]);
        } else {
            //TODO:GENERATE A 404 PAGE
            return $this->redirectToRoute('home');
        }
    }








}
