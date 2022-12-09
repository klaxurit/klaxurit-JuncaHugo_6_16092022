<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupsFixtures extends Fixture
{
    public const GROUP_GRABS_REFERENCE = 'group-grabs';
    public const GROUP_ROTATIONS_REFERENCE = 'group-rotations';
    public const GROUP_FLIPS_REFERENCE = 'groyp-flips';
    public const GROUP_SLIDES_REFERENCE = 'group-slides';

    public function load(ObjectManager $manager): void
    {
        $group = new Group();
        $group->setName("Grabs");
        $manager->persist($group);
        $this->addReference(self::GROUP_GRABS_REFERENCE, $group);

        $group = new Group();
        $group->setName("Rotations");
        $this->addReference(self::GROUP_ROTATIONS_REFERENCE, $group);
        $manager->persist($group);

        $group = new Group();
        $group->setName("Flips");
        $this->addReference(self::GROUP_FLIPS_REFERENCE, $group);
        $manager->persist($group);

        $group = new Group();
        $group->setName("Slides");
        $this->addReference(self::GROUP_SLIDES_REFERENCE, $group);
        $manager->persist($group);

        $manager->flush();
    }
}