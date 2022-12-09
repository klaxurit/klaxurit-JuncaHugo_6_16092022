<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\DataFixtures\UsersFixtures;
use App\DataFixtures\GroupsFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TricksFixtures extends Fixture implements DependentFixtureInterface
{
	public const TRICK_STALEFISH_REFERENCE = "trick-stalefish";
	public const TRICK_TAIL_GRAB_REFERENCE = "trick-tail-grab";
	public const TRICK_NOSE_GRAB_REFERENCE = "trick-nose-grab";
	public const TRICK_JAPAN_REFERENCE = "trick-japan";
	public const TRICK_TRUCK_DRIVER_REFERENCE = "trick-truck-driver";
	public const TRICK_MUTE_REFERENCE = "trick-mute";
    public const TRICK_SAD_REFERENCE = "trick-sad";
	public const TRICK_INDY_REFERENCE = "trick-indy";
	public const TRICK_SEAT_BELT_REFERENCE = "trick-seat-belt";
	public const TRICK_360_REFERENCE = "trick-360";

    public function load(ObjectManager $manager): void
    {
        $trick = new Trick();
        $trick->setName('Stalefish');
        $trick->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus consectetur cum accusantium quasi eos! Voluptatum quaerat veritatis facilis unde ab recusandae officia quibusdam corporis iusto.');
        $trick->setTrickGroup($this->getReference(GroupsFixtures::GROUP_GRABS_REFERENCE));
        $trick->setSlug('stalefish');
        $trick->setUser($this->getReference(UsersFixtures::ADMIN_REFERENCE));
        $this->addReference(self::TRICK_STALEFISH_REFERENCE, $trick);
        $manager->persist($trick);

        $trick = new Trick();
        $trick->setName('Tail Grab');
        $trick->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus consectetur cum accusantium quasi eos! Voluptatum quaerat veritatis facilis unde ab recusandae officia quibusdam corporis iusto.');
        $trick->setTrickGroup($this->getReference(GroupsFixtures::GROUP_GRABS_REFERENCE));
        $trick->setSlug('tail-grub');
        $trick->setUser($this->getReference(UsersFixtures::ADMIN_REFERENCE));
        $this->addReference(self::TRICK_TAIL_GRAB_REFERENCE, $trick);
        $manager->persist($trick);

        $trick = new Trick();
        $trick->setName('Nose Grab');
        $trick->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus consectetur cum accusantium quasi eos! Voluptatum quaerat veritatis facilis unde ab recusandae officia quibusdam corporis iusto.');
        $trick->setTrickGroup($this->getReference(GroupsFixtures::GROUP_GRABS_REFERENCE));
        $trick->setSlug('nose-grab');
        $trick->setUser($this->getReference(UsersFixtures::ADMIN_REFERENCE));
        $this->addReference(self::TRICK_NOSE_GRAB_REFERENCE, $trick);
        $manager->persist($trick);

        $trick = new Trick();
        $trick->setName('Japan');
        $trick->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus consectetur cum accusantium quasi eos! Voluptatum quaerat veritatis facilis unde ab recusandae officia quibusdam corporis iusto.');
        $trick->setTrickGroup($this->getReference(GroupsFixtures::GROUP_GRABS_REFERENCE));
        $trick->setSlug('japan');
        $trick->setUser($this->getReference(UsersFixtures::USER_REFERENCE));
        $this->addReference(self::TRICK_JAPAN_REFERENCE, $trick);
        $manager->persist($trick);

        $trick = new Trick();
        $trick->setName('Truck Driver');
        $trick->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus consectetur cum accusantium quasi eos! Voluptatum quaerat veritatis facilis unde ab recusandae officia quibusdam corporis iusto.');
        $trick->setTrickGroup($this->getReference(GroupsFixtures::GROUP_GRABS_REFERENCE));
        $trick->setSlug('truck-driver');
        $trick->setUser($this->getReference(UsersFixtures::USER_REFERENCE));
        $this->addReference(self::TRICK_TRUCK_DRIVER_REFERENCE, $trick);
        $manager->persist($trick);

        $trick = new Trick();
        $trick->setName('Mute');
        $trick->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus consectetur cum accusantium quasi eos! Voluptatum quaerat veritatis facilis unde ab recusandae officia quibusdam corporis iusto.');
        $trick->setTrickGroup($this->getReference(GroupsFixtures::GROUP_GRABS_REFERENCE));
        $trick->setSlug('mute');
        $trick->setUser($this->getReference(UsersFixtures::USER_REFERENCE));
        $this->addReference(self::TRICK_MUTE_REFERENCE, $trick);
        $manager->persist($trick);

        $trick = new Trick();
        $trick->setName('Sad');
        $trick->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus consectetur cum accusantium quasi eos! Voluptatum quaerat veritatis facilis unde ab recusandae officia quibusdam corporis iusto.');
        $trick->setTrickGroup($this->getReference(GroupsFixtures::GROUP_GRABS_REFERENCE));
        $trick->setSlug('sad');
        $trick->setUser($this->getReference(UsersFixtures::USER_REFERENCE));
        $this->addReference(self::TRICK_SAD_REFERENCE, $trick);
        $manager->persist($trick);

        $trick = new Trick();
        $trick->setName('Indy');
        $trick->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus consectetur cum accusantium quasi eos! Voluptatum quaerat veritatis facilis unde ab recusandae officia quibusdam corporis iusto.');
        $trick->setTrickGroup($this->getReference(GroupsFixtures::GROUP_GRABS_REFERENCE));
        $trick->setSlug('indy');
        $trick->setUser($this->getReference(UsersFixtures::USER_REFERENCE));
        $this->addReference(self::TRICK_INDY_REFERENCE, $trick);
        $manager->persist($trick);

        $trick = new Trick();
        $trick->setName('Seat Belt');
        $trick->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus consectetur cum accusantium quasi eos! Voluptatum quaerat veritatis facilis unde ab recusandae officia quibusdam corporis iusto.');
        $trick->setTrickGroup($this->getReference(GroupsFixtures::GROUP_GRABS_REFERENCE));
        $trick->setSlug('seat-belt');
        $trick->setUser($this->getReference(UsersFixtures::USER_REFERENCE));
        $this->addReference(self::TRICK_SEAT_BELT_REFERENCE, $trick);
        $manager->persist($trick);

        $trick = new Trick();
        $trick->setName('360');
        $trick->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus consectetur cum accusantium quasi eos! Voluptatum quaerat veritatis facilis unde ab recusandae officia quibusdam corporis iusto.');
        $trick->setTrickGroup($this->getReference(GroupsFixtures::GROUP_ROTATIONS_REFERENCE));
        $trick->setSlug('360');
        $trick->setUser($this->getReference(UsersFixtures::USER_REFERENCE));
        $this->addReference(self::TRICK_360_REFERENCE, $trick);
        $manager->persist($trick);

        $manager->flush($trick);
    }

    public function getDependencies()
    {
        return [
            GroupsFixtures::class,
            UsersFixtures::class
        ];
    }
}
