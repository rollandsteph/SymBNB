<?php

namespace App\Controller\AdminController;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAnnonceController extends AbstractController
{
    /**
     * @Route("/admin/annonces", name="admin_annonces_list")
     */
    public function listAnnonces(AnnonceRepository $repo)
    {
        $annonces=$repo->findAll();

        return $this->render('annonce/adminListAnnonces.html.twig', [
            'annonces'=> $annonces
        ]);
    }

/**
 * 
 * @Route("/admin/annonce/edit/{id}" , name="admin_annonce_edit")
 * 
 * @param Annonce $annonce
 * @return Response
 */
    public function editAnnonce(Annonce $annonce, ObjectManager $manager, Request $request){

        $form=$this->createForm(AnnonceType::class,$annonce);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($annonce);
            $manager->flush();

            $this->addFlash('success', "L'annonce a bien été modifiée !");
            return $this->redirectToRoute('admin_annonces_index');
        }

        return $this->render("annonce/adminEditAnnonce.html.twig",[
            'formAnnonce'=>$form->createView(),
            'annonce'=>$annonce,
            'bookings'=>$annonce->getBookings(),
            'comments'=>$annonce->getComments()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("/admin/annonce/delete/{id}", name = "admin_annonce_delete")
     * @param Annonce $annonce
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteAnnonce(Annonce $annonce, ObjectManager $manager){
        $nbBooking =count($annonce->getBookings());
        if($nbBooking>0){
            $this->addFlash('warning',"Vous ne pouvez pas supprimer cette annonce car ".$nbBooking." réservation(s) y sont rattachées !");
        }else{
            $manager->remove($annonce);
            $manager->flush();
            $this->addFlash('success', "l'annonce n° <strong>".$annonce->getId()." (".$annonce->getTitle().")</strong> a bien été supprimée !");
        }
        return $this->redirectToRoute('admin_annonces_list');
    }
}
