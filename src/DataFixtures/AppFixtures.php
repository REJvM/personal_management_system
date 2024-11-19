<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ){  
    }

    public function load(ObjectManager $manager): void
    {
        $adminUser = new User();
        $adminUser->setEmail('admin@test.com');
        $adminUser->setPassword(
            $this->userPasswordHasher->hashPassword(
                $adminUser, 
                'admin'
            )
        );
        $adminUser->setRoles(["ROLE_ADMIN"]);
        $manager->persist($adminUser);

        $manager->flush();
    }
}
