<?php

namespace App\Controller;

use App\Repository\PosteRepository;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    public function getStatDate($data)
    {
        $res = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        foreach ($data as $r) {
            $index = $r->getDate()->format('m');
            if ((int)$index >= 10)
                $index = $r->getDate()->format('m') - 1;
            else
                $index = $r->getDate()->format('m')[1] - 1;
            $res[$index]++;
        }
        return $res;
    }

    /**
     * @Route("/admin/home", name="adminHome")
     */
    public function admin(PosteRepository $posetRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        } else if ($user->getRoles() == ["ROLE_ADMIN"]) {
            $data = $posetRepository->findAll();
            $res = $this->getStatDate($data);
            $poste = $posetRepository->countbydate();
            $dates = [];
            $postecount = [];

            foreach ($poste as $reclame) {
                $dates[] = $reclame['date_poste'];
                $postecount[] = $reclame['count'];
            }
            return $this->render('home/admin.html.twig',
                [
                    'dates' => json_encode($dates),
                    'postecount' => json_encode($postecount),
                    'res' => $res
                ]
            );
        } else {
            return $this->redirectToRoute('error');
        }
    }

}
