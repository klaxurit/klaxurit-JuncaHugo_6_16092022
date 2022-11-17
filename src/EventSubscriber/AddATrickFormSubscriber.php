<?php

namespace App\EventSubscriber;

use App\Entity\Media;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

        if (Media::VIDEO === $data['type']) {
            return;
        } else {
            $data['fileName'] = $data['image']->getClientOriginalName();
            $form->add('fileName', TextType::class);
            return;
        }
    }
}
