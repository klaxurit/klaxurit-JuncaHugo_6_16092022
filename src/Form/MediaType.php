<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
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
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Please select a type of media.'
                        ]
                    )
                ],
                'expanded' => true,
            ])
            ->add('image', FileType::class, [
                'label'       => false,
                'mapped'      => false,
                'required'    => false,
                'constraints' => [
                    new NotBlank([
                        'groups' => ['image', 'Default'],
                        'message' => 'Please upload an image.'
                    ]),
                    new ConstraintsImage([
                        'groups' => ['image', 'Default'],
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'The file size cannot exceed {{ limit }} {{ suffix }}',
                    ]),
                ],
            ])
            ->add('alt', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => ['image', 'Default'],
                            'message' => 'Alt field cannot be blank. Please enter a valid alt.'
                        ]
                    )
                ],
            ])
            ->add('url', TextareaType::class, [
                'required'    => false,
                'label'       => false,
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => ['video', 'Default'],
                            'message' => 'Url field cannot be blank. Please enter a valid url.'
                        ]
                    )
                ],
                'attr' => [
                    'placeholder' => 'Enter the video iframe',
                    'cols' => 30,
                    'rows' => 5,
                ],
            ])
            // ->addEventSubscriber(new AddATrickFormSubscriber())
        ->getForm();
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
