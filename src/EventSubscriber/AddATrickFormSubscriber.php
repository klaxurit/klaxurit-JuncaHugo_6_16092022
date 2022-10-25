<?php

namespace App\EventSubscriber;

use App\Entity\Media;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraints\Image as ConstraintsImage;

class AddATrickFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            // FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmitData',
        ];
    }

    public function preSubmitData(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!$data['type']) {
            return;
        }

        if (Media::VIDEO === $data['type']) {
            return;
        } else {
            $data['fileName'] = $data['image']->getClientOriginalName();
            $form->add('fileName', TextType::class);
            return;
        }
    }

        // public function postSetData(FormEvent $event): void
    // {
    //     $form = $event->getForm();
    //     $form->add('type', ChoiceType::class, [
    //         'label' => 'Media type',
    //         'choices'  => [
    //             'Image' => 'Image',
    //             'Video' => 'Video',
    //         ],
    //         'choice_attr' => [
    //             'Image' => ['class' => 'btn-type'],
    //             'Video' => ['class' => 'btn-type'],
    //         ],
    //         'expanded' => true,
    //         'required' => true,
    //         'constraints' => [
    //             new NotBlank(['message' => 'Choose a media type']),
    //         ],
    //     ])
    //     ->add('image', FileType::class, [
    //         'label'       => false,
    //         'mapped'      => false,
    //         'required'    => false,
    //         'constraints' => [
    //             new ConstraintsImage([
    //                 'maxSize' => '2M',
    //                 'maxSizeMessage' => 'The file size cannot exceed {{ limit }} {{ suffix }}',
    //             ])
    //         ]
    //     ])
    //     ->add('alt', TextType::class, [
    //         'required' => false,
    //     ])
    //     ->add('url', TextareaType::class, [
    //         'required'    => false,
    //         'label'       => false,
    //         'attr' => [
    //             'placeholder' => 'Enter the video iframe',
    //             'cols' => 30,
    //             'rows' => 5,
    //         ],
    //     ]);
    // }

    // public function preSubmitData(FormEvent $event): void
    // {
    //     // dd("mort");
    //     $form = $event->getForm();
    //     $data = $event->getData();

    //     if (!$data['type']) {
    //         return;
    //     }

    //     if ($data['type'] === 'Video') {
    //         // dd("video");
    //         $form->add('url', TextareaType::class, [
    //             'required'    => true,
    //             'label'       => false,
    //             'attr' => [
    //                 'placeholder' => 'Enter the video iframe',
    //                 'cols' => 30,
    //                 'rows' => 5,
    //             ],
    //         ]);
            
    //         return;
    //     } else {
    //         $form->add('image', FileType::class, [
    //             'label'       => false,
    //             'mapped'      => false,
    //             'required'    => true,
    //             'constraints' => [
    //                 new NotBlank([
    //                     'groups' => ['image'],
    //                 ]),
    //                 new ConstraintsImage([
    //                     'maxSize' => '2M',
    //                     'maxSizeMessage' => 'The file size cannot exceed {{ limit }} {{ suffix }}',
    //                     ])
    //             ]
    //         ])
    //         ->add('alt', TextType::class, [
    //             'constraints' => [
    //                 new NotBlank([
    //                     'groups' => ['image'],
    //                 ]),
    //             ],
    //             'required' => true,
    //         ]);
                
    //         $data['fileName'] = $data['image']->getClientOriginalName();
    //         $form->add('fileName', TextType::class);

    //         // $event->setData($data);
    //         return;
    //     }
    // }
}
