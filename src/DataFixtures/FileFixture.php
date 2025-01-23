<?php

namespace App\DataFixtures;

use App\Constants\MimeTypes;
use App\Entity\File;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FileFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        $picture1 = new File();
        $picture1->setName('Lion');
        $picture1->setDescription('Picture of a lion');
        $picture1->setType(MimeTypes::JPEG);
        $picture1->setPath('lion-6791ff5095433.jpg');
        $picture1->setUser($this->getReference(UserFixtures::USER_REFERENCE, User::class));
        $manager->persist($picture1);

        $picture2 = new File();
        $picture2->setName('Lion cub');
        $picture2->setDescription('Picture of a lion cub');
        $picture2->setType(MimeTypes::JPEG);
        $picture2->setPath('lion-cub-6791ff6632af7.jpg');
        $picture2->setUser($this->getReference(UserFixtures::USER_REFERENCE, User::class));
        $manager->persist($picture2);

        $picture3 = new File();
        $picture3->setName('Black lion');
        $picture3->setDescription('Picture of a black lion');
        $picture3->setType(MimeTypes::JPEG);
        $picture3->setPath('black-lion-6791ff7c753f4.jpg');
        $picture3->setUser($this->getReference(UserFixtures::USER_REFERENCE, User::class));
        $manager->persist($picture3);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
