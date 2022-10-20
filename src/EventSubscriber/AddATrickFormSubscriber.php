<?php

namespace App\EventSubscriber;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraints\Image as ConstraintsImage;

class AddATrickFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
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

        if ($data['type'] === 'Video') {
            unset($data['image']);
            unset($data['alt']);
            $form->add('url', TextareaType::class, [
                'required'    => false,
                'label'       => false,
                'attr' => [
                    'placeholder' => 'Enter the video iframe',
                    'cols' => 30,
                    'rows' => 5,
                ],
            ]);
            $event->setData($data);
            // dd($data);
            return;
        } else {
            unset($data['url']);
            $form->add(
                'alt',
                TextType::class,
                [
                    'required' => false,
                ]
            );
            $form->add(
                'image',
                FileType::class,
                [
                    'label'       => false,
                    'mapped'      => false,
                    'required'    => false,
                    'constraints' => [
                        new ConstraintsImage([
                            'maxSize' => '2M',
                            'maxSizeMessage' => 'The file size cannot exceed {{ limit }} {{ suffix }}',
                        ])
                    ]
                ]
            );

            $data['fileName'] = $data['image']->getClientOriginalName();
            $form->add('fileName', TextType::class);

            $event->setData($data);
            return;
        }
    }
}
