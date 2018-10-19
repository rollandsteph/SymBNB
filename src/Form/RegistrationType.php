<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class,$this->setConfiguration("Prénom","Saisir votre prénom"))
            ->add('lastName',TextType::class,$this->setConfiguration("Nom","Saisir votre nom"))
            ->add('email',EmailType::class,$this->setConfiguration('Email',"Saisir votr email"))
            ->add('picture',UrlType::class,$this->setConfiguration('Avatar',"Url de votre avatar"))
            ->add('hash',PasswordType::class,$this->setConfiguration('Mot de passe',"Saisir un mot de passe"))
            ->add('passwordConfirm',PasswordType::class,$this->setConfiguration('Confirmation de mot de passe',"Veuillez confirmer votre mot de passe"))
            ->add('introduction',TextType::class,$this->setConfiguration('Introduction',"Présentez vous en quelques mots"))
            ->add('description',TextareaType::class,$this->setConfiguration('Description détaillée',"C'est le moment de vous présenter en détail"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
