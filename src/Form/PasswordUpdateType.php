<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->setConfiguration('Ancien mot de passe',"Saisir votre mot de passe actuel"))
            ->add('newPassword', PasswordType::class, $this->setConfiguration('Nouveau mot de passe',"Saisir votre nouveau mot de passe"))
            ->add('confirmPassword', PasswordType::class, $this->setConfiguration('confirmation de votre nouveau mot de passe',"Confirmez votre nouveau mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
