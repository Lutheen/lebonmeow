<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'input bg-white-darker',
                    'placeholder' => 'Que recherchez-vous ?'
                ]
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'button button-full p07-1em'
                ]
            ])
        ;
    }
}