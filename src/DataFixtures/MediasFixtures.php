<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\DataFixtures\TricksFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MediasFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $media = new Media();
        $media->setType('Image');
        $media->setFilename('stalefish.jpg');
        $media->setAlt('stale fish trick');
        $media->setTrick($this->getReference(TricksFixtures::TRICK_STALEFISH_REFERENCE));
        $manager->persist($media);

        $media = new Media();
        $media->setType('Image');
        $media->setFilename('backsideair.jpg');
        $media->setAlt('back side air trick');
        $media->setTrick($this->getReference(TricksFixtures::TRICK_360_REFERENCE));
        $manager->persist($media);

        $media = new Media();
        $media->setType('Image');
        $media->setFilename('tailgrab.jpg');
        $media->setAlt('tail grab trick');
        $media->setTrick($this->getReference(TricksFixtures::TRICK_TAIL_GRAB_REFERENCE));
        $manager->persist($media);

        $media = new Media();
        $media->setType('Image');
        $media->setFilename('truckdriver.jpg');
        $media->setAlt('truck driver trick');
        $media->setTrick($this->getReference(TricksFixtures::TRICK_TRUCK_DRIVER_REFERENCE));
        $manager->persist($media);

        $media = new Media();
        $media->setType('Image');
        $media->setFilename('hakonflip.jpg');
        $media->setAlt('hakon flip trick');
        $media->setTrick($this->getReference(TricksFixtures::TRICK_SEAT_BELT_REFERENCE));
        $manager->persist($media);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TricksFixtures::class,
        ];
    }
}
https://www.youtube.com/embed/8KotvBY28Mo