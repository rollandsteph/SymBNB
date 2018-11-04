<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', TextType::class,$this->setConfiguration("Date d'arrivée","Saisir la date à laquelle vous comptez arriver"))
            ->add('endDate', TextType::class, $this->setConfiguration("Date de départ","Saisir la date à laquelle vous quittez les lieux"))
            ->add('comment', TextareaType::class, $this->setConfiguration(false,"veuillez un commentaire si nécessaire",["required"=>false]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
