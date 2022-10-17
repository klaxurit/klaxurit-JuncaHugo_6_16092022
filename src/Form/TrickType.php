<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,
            [
                'required' => true
            ])
            ->add('description', TextareaType::class,
            [
                'required' => true
            ])
            ->add('medias', CollectionType::class, [
                'entry_type'   => MediaType::class,
                'by_reference' => false,
                'allow_add'    => true,
                'allow_delete' => true
            ])
            ->add('groups', EntityType::class, [
                'class'        => Group::class,
                'choice_label' => 'name',
                'mapped'       => false,
                'by_reference' => false,
                'multiple'     => false,
                'expanded'     => true,
                'required'     => true
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
