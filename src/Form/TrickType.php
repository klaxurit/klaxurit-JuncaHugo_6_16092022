<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,
            [
                'required' => true,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Trick\'s name field cannot be blank. Please enter a valid name for your trick.'
                        ]
                    )
                ],
            ])
            ->add('description', TextareaType::class,
            [
                'required' => true,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Description field cannot be blank. Please enter a valid description for your trick.'
                        ]
                    )
                ],
            ])
            ->add('medias', CollectionType::class, [
                'entry_type'   => MediaType::class,
                'constraints' => [
                    new Valid()
                ],
                'mapped' => false,
                'by_reference' => false,
                'allow_add'    => true,
                'allow_delete' => true,
                'error_bubbling' => true
            ])
            ->add('groups', EntityType::class, [
                'class'        => Group::class,
                'choice_label' => 'name',
                'mapped'       => false,
                'by_reference' => false,
                'multiple'     => false,
                'expanded'     => true,
                'required'     => true,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Please select a group for your trick.'
                        ]
                    )
                ],
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
