<?php

namespace App\Controller\AdminController;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_bookings_list")
     */
    public function listBookings(BookingRepository $repo)
    {
        $bookings=$repo->findAll();
        return $this->render('booking/adminListBookings.html.twig', [
            'bookings'=>$bookings
        ]);
    }

    /**
     * Permet d'éditer une réservation
     *
     * @Route("/admin/bookings/edit/{id}", name="admin_booking_edit")
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editBooking(Booking $booking, Request $request, ObjectManager $manager){
        $form=$this->createForm(AdminBookingType::class,$booking, [
            'validation_groups'=> ["default"]
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($booking); //pas nécessaire
            $manager->flush();
            $this->addFlash('success',"La réservation n°". $booking->getId() ." a bien été modifiée.");
            return $this->redirectToRoute('admin_bookings_list');
        }
        return $this->render('booking/adminEditBooking.html.twig',[
            'formBooking'   => $form->createView(),
            'booking'       => $booking
        ]);
    }
}
