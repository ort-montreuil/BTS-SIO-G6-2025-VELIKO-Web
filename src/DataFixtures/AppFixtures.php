<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("user-admin-@gmail.dev");
        $user->setPassword($this->passwordHasher->hashPassword($user, "Bonjour12345!"));
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setNom("nom-admin");
        $user->setPrenom("prenom-admin");
        $user->setDateNaissance(new \DateTime());
        $user->setAdresse("adresse-admin");
        $user->setCodePostale("92100");
        $user->setVille("ville-admin");
        $user->setVerified(true);

        $manager->persist($user);

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user-$i@gmail.dev");
            $user->setPassword($this->passwordHasher->hashPassword($user, "Bonjour12345!"));
            $user->setRoles([]);
            $user->setNom("nom-$i");
            $user->setPrenom("prenom-$i");
            $user->setDateNaissance(new \DateTime());
            $user->setAdresse("adresse-$i");
            $user->setCodePostale("92100");
            $user->setVille("ville-$i");
            $user->setVerified(true);
            $manager->persist($user);
        }
        $manager->flush();
    }
}