<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\AnnonceRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function home(AnnonceRepository $AnnonceRepo, UserRepository $userRepo)
    {
        return $this->render('home/home.html.twig', [
            'bestAnnonces'  => $AnnonceRepo->findBestAnnonces(5),
            'bestUsers'     => $userRepo->findBestUsers(2)
        ]);
    }
}
