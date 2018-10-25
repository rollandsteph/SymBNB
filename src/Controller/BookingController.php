<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Booking;
use App\Form\BookingType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/annonces/{slug}/book", name="booking_create")
     */
    public function book(Annonce $annonce, Request $request, ObjectManager $manager)
    {
        $booking=new Booking();
        $form=$this->createForm(BookingType::class,$booking);

        $form->handleRequest($request); // fait le mapping

        if( $form->isSubMitted() && $form->isValid() ){
            
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash('success', "Votre réservation a bien été enregistrée.");

            // rediriger vers la route permettant de visualiser l'annonce
            return $this->redirectToRoute('annonces_show', [
                'slug' => $annonce->getSlug() // on passe le paramètre nécessaire à la route qui est le slug
            ]);
        }

        return $this->render('booking/book.html.twig', [
            'annonce'=>$annonce,
            'form'=>$form->createView()
        ]);
    }
}
