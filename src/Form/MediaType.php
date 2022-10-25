<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use App\EventSubscriber\AddATrickFormSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Image as ConstraintsImage;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices'  => [
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
                'label'       => false,
                'mapped'      => false,
                'required'    => false,
                'constraints' => [
                    new ConstraintsImage([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'The file size cannot exceed {{ limit }} {{ suffix }}',
                    ])
                ]
            ])
            ->add('alt', TextType::class, [
                'required' => false,
            ])
            ->add('url', TextareaType::class, [
                'required'    => false,
                'label'       => false,
                'attr' => [
                    'placeholder' => 'Enter the video iframe',
                    'cols' => 30,
                    'rows' => 5,
                ],
            ])
            ->addEventSubscriber(new AddATrickFormSubscriber())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                if (Media::VIDEO == $data->getType()) {
                    return ['video'];
                }

                return ['image'];
            },
        ]);
    }
}
