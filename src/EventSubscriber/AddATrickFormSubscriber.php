<?php 

namespace App\EventSubscriber;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraints\Image as ConstraintsImage;

class AddATrickFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'preSubmitData',
        ];
    }

    public function onPreSetData(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();
        

        $form->add('image', FileType::class, [
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
        ->add('url', TextareaType::class, [
            'required'    => false,
            'label'       => false,
            'attr' => [
                'placeholder' => 'Enter the video iframe',
                'cols' => 30,
                'rows' => 5,
            ],
        ])
        ->add('alt', TextType::class,
        [
            'required' => false,
        ]);

        // checks whether the user from the initial data has chosen to
        // display their email or not.
        // if (true === $user->isShowEmail()) {
        //     $form->add('email', EmailType::class);
        // }
    }

    public function preSubmitData(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();
        if (!$data['type']){
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
            $form->add('alt', TextType::class,
            [
                'required' => false,
            ]);
            $form->add('image', FileType::class, [
                'label'       => false,
                'mapped'      => false,
                'required'    => false,
                'constraints' => [
                    new ConstraintsImage([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'The file size cannot exceed {{ limit }} {{ suffix }}',
                        ])
                        ]
                    ]);
                    // $event->setData($data);
                    // dd($event);
            return;
        }
    }
}