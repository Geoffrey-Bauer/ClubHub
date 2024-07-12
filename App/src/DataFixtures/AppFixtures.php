<?php

namespace App\DataFixtures;

use App\Entity\Team;
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
        ->setFirstname('Bauer')
        ->setMail("admin@clubhub.fr")
        ->setPassword($this->encoder->hashPassword($user, 'Azerty123'))
        ->setCoach(0)
        ->setAdmin(1);
      $manager->persist($user);

      $user = new User();
      $user->setLastname('Fouad')
        ->setFirstname('Taibi')
        ->setMail("coach@clubhub.fr")
        ->setPassword($this->encoder->hashPassword($user, 'Azerty123'))
        ->setCoach(1)
        ->setAdmin(0);
      $manager->persist($user);

      $user = new User();
      $user->setLastname('Odelin')
        ->setFirstname('Raffault')
        ->setMail("user@clubhub.fr")
        ->setPassword($this->encoder->hashPassword($user, 'Azerty123'))
        ->setCoach(0)
        ->setAdmin(0);
      $manager->persist($user);

      $team = new Team();
      $team->setName('Paris Saint-Germain');

      $manager->flush();
    }
}
