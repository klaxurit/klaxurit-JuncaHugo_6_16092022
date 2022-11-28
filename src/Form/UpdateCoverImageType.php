<?php

namespace App\Form;

use App\Entity\Media;
use App\Entity\Trick;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateCoverImageType extends AbstractType
{
    private $mediaRepository;
    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $trickId = $builder->getData()->getId();
        $builder
            ->add('cover_image', EntityType::class, [
                'class'        => Media::class,
                'choices'      => $this->mediaRepository->findAllMediaImageOfATrick($trickId),
                'multiple'     => false,
                'expanded'     => true,
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
