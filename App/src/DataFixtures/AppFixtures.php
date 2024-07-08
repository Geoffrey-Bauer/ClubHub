<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
      $this->encoder = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
      $user = new User();
      $user->setLastname('Geoffrey')
        ->setFistname('Bauer')
        ->setMail("admin@psg.fr")
        ->setPassword($this->encoder->hashPassword($user, 'Azerty123'))
        ->setCoach(0)
        ->setAdmin(1);
      $manager->persist($user);

      $manager->flush();
    }
}
