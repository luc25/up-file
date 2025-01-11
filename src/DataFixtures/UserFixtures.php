<?php

namespace App\DataFixtures;

use App\Constants\Roles;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
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
        $admin = new User();
        $admin->setEmail('admin@upfile.com');
        $password = $this->hasher->hashPassword($admin, 'admin_upfile');
        $admin->setPassword($password);
        $admin->setRoles([Roles::ROLE_ADMIN]);
        $this->addReference(self::ADMIN_REFERENCE, $admin);
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@upfile.com');
        $password = $this->hasher->hashPassword($user, 'user_upfile');
        $user->setPassword($password);
        $this->addReference(self::USER_REFERENCE, $user);
        $manager->persist($user);

        $manager->flush();
    }
}
