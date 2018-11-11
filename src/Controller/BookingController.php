<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * Enregistre une réservation sur une annonce
     * 
     * @Route("/annonces/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Annonce $annonce, Request $request, ObjectManager $manager)
    {
        $booking=new Booking();
        $form=$this->createForm(BookingType::class,$booking,[
            'validation_groups'=>['Default','front']
        ]);

        $form->handleRequest($request); // fait le mapping

        if( $form->isSubMitted() && $form->isValid() ){
            
            $booking->setAnnonce($annonce)
                    ->setBooker($this->getUser());
            // si les dates sont possibles
            if($booking->isBookableDates()){
                
                $manager->persist($booking);
                $manager->flush();
                // rediriger vers la route permettant de visualiser l'annonce
                return $this->redirectToRoute('booking_show', [
                    'id' => $booking->getId(), // on passe le paramètre nécessaire à la route qui est le slug
                    'withAlert' => true // permet d'afficher une alerte uniquement lorsque l'on vient de réserver
                ]);
            }else
            {
                $this->addFlash('warning', "Le bien est déjà réservé durant l'un des jours proposés. Veuillez modifier vos dates");
            }

        }

        return $this->render('booking/book.html.twig', [
            'annonce'=>$annonce,
            'form'=>$form->createView()
        ]);
    }
/**
 * Permet d'afficher la page de réservation
 * 
 *@Route("/booking/{id}", name="booking_show")
 * @param Booking $booking
 * @return Response
 */
    public function show(Booking $booking,Request $request, ObjectManager $manager)
    {
        $comment=new Comment();
        $form=$this->createForm(CommentType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setAuthor($this->getUser())
                    ->setAnnonce($booking->getAnnonce());
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success','Votre commentaire a bien été pris en compte !');

        }
        return $this->render('booking/show.html.twig',[
            'booking'=>$booking,
            'formComment'=>$form->createView()
            ]);
    }
}
