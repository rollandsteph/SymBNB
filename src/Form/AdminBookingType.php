<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Booking;
use App\Form\BookingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('booker', EntityType::class,[
                'class'=> User::class,
                'choice_label'=> function($user){
                    return $user->getFirstName()." ". strtoupper($user->getLastName());
                }
            ])
            ->add('annonce', EntityType::class, [
                'class'=>Annonce::class,
                'choice_label'=> 'title'
            ])
        ;
    }

    public function getParent()
    {
        return BookingType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
