<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class BookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class,$this->setConfiguration("Date d'arrivée","Saisir la date à laquelle vous comptez arriver",['widget'=>"single_text"]))
            ->add('endDate', DateType::class, $this->setConfiguration("Date de départ","Saisir la date à laquelle vous quittez les lieux",['widget'=>"single_text"]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
