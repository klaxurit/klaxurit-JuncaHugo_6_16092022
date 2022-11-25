<?php

namespace App\Form;

use App\Entity\Media;
use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateCoverImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cover_image', EntityType::class, [
                'class'        => Media::class,
                // 'choice_label' => function (Trick $trick) {
                //     $images = $trick->getMedias();
                //     // dd($images->getValues());
                //     // dd($images->getValues());
                //     // dd($images->getValues());
                //     foreach ($images->getValues() as $image) {
                //         $trickImages["fileName"] = $image->getFileName();
                //         // dd($trickImages);
                //         // var_dump("ici");
                //     }
                //     return $trickImages["fileName"];
                // },
                'choice_label' => 'fileName',
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
