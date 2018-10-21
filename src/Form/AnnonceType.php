<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,$this->setConfiguration('Titre',"Saisir le titre de l'annonce"))
            ->add('slug',TextType::class,$this->setConfiguration("Slug","Saisir le slug de l'annonce",['required'=>false]))
            ->add('price',MoneyType::class,$this->setConfiguration("Prix","Saisir le prix par nuit"))
            ->add('introduction',TextType::class,$this->setConfiguration('Introduction',"Saisir l'introduction de l'annonce"))
            ->add('content',TextareaType::class,$this->setConfiguration('Contenu',"Saisir le contenu de l'annonce"))
            ->add('coverImage',UrlType::class,$this->setConfiguration("Url de l'image","Saisir l'adresse de l'image"))
            ->add('rooms',IntegerType::class,$this->setConfiguration("Nombre de chambres","Saisir le nombre de chambres"))
            ->add('images', CollectionType::class,[
                'entry_type'=> ImageType::class,
                'allow_add'=>true,
                'allow_delete'=>true,
                'by_reference'=> false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
