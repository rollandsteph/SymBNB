<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAnnonceController extends AbstractController
{
    /**
     * @Route("/admin/annonces", name="admin_annonces_index")
     */
    public function index(AnnonceRepository $repo)
    {
        $annonces=$repo->findAll();

        return $this->render('admin/annonce/index.html.twig', [
            'annonces'=> $annonces
        ]);
    }
}
