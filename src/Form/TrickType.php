<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('medias', CollectionType::class, [
                'entry_type' => MediaType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true
            ])
            // ->add('images', FileType::class, [
            //     'label' => false,
            //     'multiple' => true,
            //     'mapped' => false,
            //     'required' => false
            // ])
            // ->add('picture_alt')
            ->add('groups', CollectionType::class, [
                'entry_type'   => ChoiceType::class,
                'entry_options'  => [
                    'choices'  => [
                        'Nashville' => 'nashville',
                        'Paris'     => 'paris',
                        'Berlin'    => 'berlin',
                        'London'    => 'london',
                    ],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
