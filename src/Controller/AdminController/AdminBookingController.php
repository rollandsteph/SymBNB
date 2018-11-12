<?php

namespace App\Controller\AdminController;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\PaginationService;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page}", name="admin_bookings_list", requirements={"page":"\d+"})
     */
    public function listBookings(PaginationService $pagination, $page=1)
    {
        $pagination	->setEntityClass(Booking::class);

        return $this->render('booking/adminListBookings.html.twig', [
            'pagination'=>$pagination
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

        /**
     * Permet de supprimer une reservation
     *
     * @Route("/admin/booking/delete/{id}", name = "admin_booking_delete")
     * @param Annonce $annonce
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteBooking(Booking $booking, ObjectManager $manager){

        $manager->remove($booking);
        $manager->flush();
        $this->addFlash('success', "l'annonce n° <strong>".$booking->getId()."</strong> a bien été supprimée !");
        return $this->redirectToRoute('admin_bookings_list');
    }
}
