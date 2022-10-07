<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Image' => 'Image',
                    'Video' => 'Video',
                ],
                'choice_attr' => [
                    'Image' => ['class' => 'btn-type'],
                    'Video' => ['class' => 'btn-type'],
                ],
                'expanded' => true,
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('videoUrl', TextareaType::class, [
                'mapped' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Enter the video iframe',
                    'cols' => 30,
                    'rows' => 5,
                ],
            ])
            ->add('url')
            ->add('fileName')
            ->add('alt')
            ->add('trick')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
