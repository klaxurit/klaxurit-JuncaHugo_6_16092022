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
        dd("mort");
        $form = $event->getForm();
        $data = $event->getData();

        if (!$data['type']) {
            return;
        }

        if ($data['type'] === 'Video') {
            // dd("video");
            $form->add('url', TextareaType::class, [
                'required'    => true,
                'label'       => false,
                'attr' => [
                    'placeholder' => 'Enter the video iframe',
                    'cols' => 30,
                    'rows' => 5,
                ],
            ]);
            // $event->setData($data);
            // dd($data);
            return;
        } else {
            dd("image");
            $form->remove('url', TextareaType::class, [
                'required'    => false,
                'label'       => false,
                'attr' => [
                    'placeholder' => 'Enter the video iframe',
                    'cols' => 30,
                    'rows' => 5,
                ],
            ]);
            

            $data['fileName'] = $data['image']->getClientOriginalName();
            $form->add('fileName', TextType::class);

            // $event->setData($data);
            return;
        }
    }
}
