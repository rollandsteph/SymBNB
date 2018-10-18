<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads=$repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }
    
        /**
         * Permet de créer une annonce
         * @Route("/ads/new", name="ads_create")
         *
         * @return void
         */
        public function create()
        {
            return $this->render('ad/new.html.twig');
        }

    /**
     * permet d'afficher une annonce
     *
     * @Route("/ads/{slug}", name="ads_show")
     * @return void
     */
    public function show(Ad $ad)
    {
        return $this->render('ad/show.html.twig',[
            'ad'=>$ad
        ]);
    }

}
