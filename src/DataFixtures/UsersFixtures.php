<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    public const ADMIN_REFERENCE = 'ADMIN';
    public const USER_REFERENCE = 'USER';

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('Admin');
        $user->setEmail('admin@admin.com');
        $user->setPassword($this->hasher->hashPassword($user, 'Admin_1234'));
        $user->setRoles(['ROLE_ADMIN']);
        $this->addReference(self::ADMIN_REFERENCE, $user);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('UserOne');
        $user->setEmail('userone@userone.com');
        $user->setPassword($this->hasher->hashPassword($user, 'Userone_1234'));
        $user->setRoles(['ROLE_USER']);
        $this->setReference(self::USER_REFERENCE, $user);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('UserTwo');
        $user->setEmail('usertwo@usertwo.com');
        $user->setPassword($this->hasher->hashPassword($user, 'Usertwo_1234'));
        $user->setRoles(['ROLE_USER']);
        $this->setReference(self::USER_REFERENCE, $user);
        $manager->persist($user);

        $manager->flush();
    }
}
