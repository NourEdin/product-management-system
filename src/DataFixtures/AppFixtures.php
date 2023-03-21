<?php

namespace App\DataFixtures;

use App\Entity\Credential;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;
    function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->hasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername("admin");
        $hashed = $this->hasher->hashPassword(
            $user,
            "admin"
        );
        $user->setPassword($hashed);

        $cred = new Credential();
        $cred->setClientId("test_client_id");
        $cred->setSecret("test_client_secret");
        $cred->setUser($user);

        $manager->persist($user);
        $manager->persist($cred);

        $manager->flush();
    }
}
