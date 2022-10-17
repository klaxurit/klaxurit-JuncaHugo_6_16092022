<?php 

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddATrickFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $type = $event->getData();
        // dd($type);
        if (!$type){
            return;
        }
        if ($type === 'Video') {
            $form->remove('alt');
            $form->remove('image');
            $form->add('videoUrl');
            return;
        } else {
            $form->remove('videoUrl');
            $form->add('alt');
            $form->add('image');
            return;
        }
    }
}