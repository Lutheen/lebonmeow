<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-field'
                ],
                'row_attr' => [
                    'class' => 'form-group'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Décrivez votre article',
                    'class' => 'form-field',
                    'rows' => 10
                ],
                'row_attr' => [
                    'class' => 'form-group'
                ]
            ])
            ->add('price', MoneyType::class, [
                'currency' => false,
                'attr' => [
                    'placeholder' => '€'
                ]
            ])
            ->add('firstImage', DropzoneType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false
                ],
                'constraints' => [
                    new Image([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Merci de fournir une image JPEG ou PNG.'
                    ])
                ],
                'row_attr' => [
                    'class' => 'form-group'
                ]
            ])
            ->add('secondImage', DropzoneType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Faites glisser ou cliquez pour ajouter vos photos ici'
                ],
                'constraints' => [
                    new Image([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Merci de fournir une image JPEG ou PNG.'
                    ])
                ],
                'row_attr' => [
                    'class' => 'form-group'
                ]
            ])
            ->add('thirdImage', DropzoneType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false
                ],
                'constraints' => [
                    new Image([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Merci de fournir une image JPEG ou PNG.'
                    ])
                ],
                'row_attr' => [
                    'class' => 'form-group'
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-field'
                ],
                'row_attr' => [
                    'class' => 'form-group'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

// Dropzone multiple file uploads https://github.com/symfony/ux/pull/512
// Branch not merged yet