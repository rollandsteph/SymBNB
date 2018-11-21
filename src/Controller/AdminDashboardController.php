<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, StatsService $mesStats)
    {
        $bestAnnonces=$mesStats->getAnnonces();
        $badAnnonces=$mesStats->getAnnonces('ASC');

        return $this->render('admin/dashboard/index.html.twig',[
            // on peut renvoyer un tableau de stat et notamment en utilisant la fonction compact
            // cette fonction permet de créer un tableau avec des clés et valeur qui ont le même nom
            // exemple ici j'ai une variable "$nbUser" (la valeur) et je veux lui donner une clé "nbUsers"
                'stats'          =>  $mesStats->getStats(),
                'bestAnnonces'  =>  $bestAnnonces,
                'badAnnonces'   => $badAnnonces

        ]);

    }
}
