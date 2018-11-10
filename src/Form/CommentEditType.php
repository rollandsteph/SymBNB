<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('rating')
        ;
    }

    /**
     * permet de récupérer le formulaire d'édition pour le client
     * auquel on enlève la note que nous décidons de ne pouvoir modifier
     *
     * @return void
     */
    public function getParent()
    {
        return CommentType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
