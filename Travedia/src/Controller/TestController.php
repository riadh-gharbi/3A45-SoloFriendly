<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/basefront", name="basefront")
     */
    public function index(): Response
    {
        return $this->render('basefront.html.twig', [
            'controller_name' => 'TestController',
        ]);

    }
        /**
         * @Route("/baseback", name="baseback")
         */
        public function afficher (): Response
    {
        return $this->render('baseback.html.twig', [
            'controller_name' => 'TestController',
        ]);


    }
}
