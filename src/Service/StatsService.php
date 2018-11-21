<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class StatsService {

    private $manager;

    public function __construct(ObjectManager $manager){
        $this->manager=$manager;
    }

    /**
     * retourne le nombre d 'utilisateurs
     *
     * @return array
     */
    public function getUsersCount(){
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }
    /**
     * retourne le nombre d'annonces
     *
     * @return array
     */
    public function getAnnoncesCount(){
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Annonce a')->getSingleScalarResult();
    }
    /**
     * retourne le nombre de réservations
     *
     * @return array
     */
    public function getBookingsCount(){
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }
    /**
     * retourne le nombre de commentaires
     *
     * @return array
     */
    public function getCommentsCount(){
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }
  
    /**
     * retourne les annonces les plus populaires
     *
     * @param string $order (DESC par défaut pour les meilleurs annonces)
     * @return void
     */
    public function getAnnonces($order='DESC'){
        return $this->manager	->createQuery(
                                            "SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
                                            FROM App\Entity\Comment c
                                            JOIN c.annonce a
                                            JOIN a.author u
                                            GROUP BY a
                                            ORDER BY note ". $order
                                            )
                                ->setMaxResults(5)
                                ->getResult();
    }
    /**
     * retourne l'ensemble des statistiques'
     *
     * @return array
     */
    public function getStats(){
        $nbUsers=$this->getUsersCount();
        $nbAnnonces=$this->getAnnoncesCount();
        $nbBookings=$this->getBookingsCount();
        $nbComments=$this->getCommentsCount();
        return compact('nbUsers','nbAnnonces','nbBookings','nbComments');
    }
}