<?php

namespace App\DataFixtures;

use App\Entity\Player;
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
      $this->loadUser($manager);

      $this->loadTeam($manager);

      $this->loadPlayer($manager);

      $manager->flush();
    }

  public function loadUser(ObjectManager $manager): void
  {
    $data_user = [
      ['lastname' => 'Geoffrey',
        'firstname' => 'Bauer',
        'mail' => 'admin@clubhub.fr',
        'password' => 'Azerty123',
        'coach' => 0,
        'admin' => 1
      ],

      ['lastname' => 'Fouad',
        'firstname' => 'Taibi',
        'mail' => 'coach@clubhub.fr',
        'password' => 'Azerty123',
        'coach' => 1,
        'admin' => 0
      ],

      ['lastname' => 'Odelin',
        'firstname' => 'Raffault',
        'mail' => 'user@clubhub.fr',
        'password' => 'Azerty123',
        'coach' => 0,
        'admin' => 0
      ]
    ];

    foreach ($data_user as $key => $value)
    {
      $user = new User();
      $user->setLastname($value['lastname'])
        ->setFirstname($value['firstname'])
        ->setMail($value['mail'])
        ->setPassword($this->encoder->hashPassword($user, $value['password']))
        ->setCoach($value['coach'])
        ->setAdmin($value['admin']);
      $manager->persist($user);


    }

  }

  public function loadTeam(ObjectManager $manager): void
  {
    $data_team = [
      [
        'name' => 'Paris Saint-Germain',
        'image' => 'psg.png'
      ],
      [
        'name' => 'Olympique de Marseille',
        'image' => 'marseille.jpg'
      ],
      [
        'name' => 'Olympique Lyonnais',
        'image' => 'lyon.jpg'
      ],
      [
        'name' => 'Monaco',
        'image' => 'monaco.jpg'
      ]
    ];

    foreach ($data_team as $key => $value)
    {
      $team = new Team();
      $team->setName($value['name'])
        ->setImagePath($value['image']);
      $manager->persist($team);
      $this->setReference('team_' . $key + 1, $team);
    }
  }

  public function loadPlayer(ObjectManager $manager): void
  {
    $data_player = [
      [
        'lastname' => 'Donnarumma',
        'firstname' => 'Gianluigi',
        'position' => 'Gardien',
        'image' => 'donnarumma.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Hakimi',
        'firstname' => 'Achraf',
        'position' => 'Défenseur',
        'image' => 'hakimi.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Marquinhos',
        'firstname' => 'Marcos',
        'position' => 'Défenseur',
        'image' => 'marquinhos.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Hernández',
        'firstname' => 'Lucas',
        'position' => 'Défenseur',
        'image' => 'hernandez.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Mendes',
        'firstname' => 'Nuno',
        'position' => 'Défenseur',
        'image' => 'nuno.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Zaïre-Emery',
        'firstname' => 'Warren',
        'position' => 'Milieu',
        'image' => 'warren.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Ugarte',
        'firstname' => 'Manuel',
        'position' => 'Milieu',
        'image' => 'ugarte.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Vitinha',
        'firstname' => 'Vítor',
        'position' => 'Milieu',
        'image' => 'vitinha.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Barcola',
        'firstname' => 'Bradley',
        'position' => 'Attaquant',
        'image' => 'barcola.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Dembélé',
        'firstname' => 'Ousmane',
        'position' => 'Attaquant',
        'image' => 'dembele.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Gonçalos',
        'firstname' => 'Ramos',
        'position' => 'Attaquant',
        'image' => 'ramos.jpg',
        'team' => 'team_1'
      ],
      [
        'lastname' => 'Lopez',
        'firstname' => 'Pau',
        'position' => 'Gardien',
        'image' => 'lopez.jpg',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Clauss',
        'firstname' => 'Jonathan',
        'position' => 'Défenseur',
        'image' => 'clauss.webp',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Mbemba',
        'firstname' => 'Chancel',
        'position' => 'Défenseur',
        'image' => 'mbemba.webp',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Balerdi',
        'firstname' => 'Leonardo',
        'position' => 'Défenseur',
        'image' => 'balerdi.webp',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Lodi',
        'firstname' => 'Renan',
        'position' => 'Défenseur',
        'image' => 'lodi.jpg',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Veretout',
        'firstname' => 'Jordan',
        'position' => 'Milieu',
        'image' => 'veretout.webp',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Rongier',
        'firstname' => 'Valentin',
        'position' => 'Milieu',
        'image' => 'rongier.webp',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Harit',
        'firstname' => 'Amine',
        'position' => 'Milieu',
        'image' => 'harit.webp',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Aubameyang',
        'firstname' => 'Pierre-Emerick',
        'position' => 'Attaquant',
        'image' => 'aubameyang.jpg',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Vitinha',
        'firstname' => 'Vítor',
        'position' => 'Attaquant',
        'image' => 'vitor.webp',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Sarr',
        'firstname' => 'Ismaïla',
        'position' => 'Attaquant',
        'image' => 'sarr.jpg',
        'team' => 'team_2'
      ],
      [
        'lastname' => 'Lopes',
        'firstname' => 'Anthony',
        'position' => 'Gardien',
        'image' => 'anthony.webp',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Kumbedi',
        'firstname' => 'Saël',
        'position' => 'Défenseur',
        'image' => 'kumbedi.webp',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Lovren',
        'firstname' => 'Dejan',
        'position' => 'Défenseur',
        'image' => 'lovren.webp',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Caleta-Car',
        'firstname' => 'Duje',
        'position' => 'Défenseur',
        'image' => 'caleta-car.webp',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Tagliafico',
        'firstname' => 'Nicolás',
        'position' => 'Défenseur',
        'image' => 'tagliafico.jpg',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Caqueret',
        'firstname' => 'Maxence',
        'position' => 'Milieu',
        'image' => 'caqueret.webp',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Tolisso',
        'firstname' => 'Corentin',
        'position' => 'Milieu',
        'image' => 'tolisso.webp',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Cherki',
        'firstname' => 'Rayan',
        'position' => 'Milieu',
        'image' => 'cherki.webp',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Lacazette',
        'firstname' => 'Alexandre',
        'position' => 'Attaquant',
        'image' => 'lacazette.webp',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Baldé',
        'firstname' => 'Malick',
        'position' => 'Attaquant',
        'image' => 'balde.jpg',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Nuamah',
        'firstname' => 'Ernest',
        'position' => 'Attaquant',
        'image' => 'nuamah.jpg',
        'team' => 'team_3'
      ],
      [
        'lastname' => 'Köhn',
        'firstname' => 'Philipp',
        'position' => 'Gardien',
        'image' => 'kohn.webp',
        'team' => 'team_4'
      ],
      [
        'lastname' => 'Vanderson',
        'firstname' => 'Jr',
        'position' => 'Défenseur',
        'image' => 'vanderson.jpg',
        'team' => 'team_4'
      ],
      [
        'lastname' => 'Maripán',
        'firstname' => 'Guillermo',
        'position' => 'Défenseur',
        'image' => 'maripan.jpg',
        'team' => 'team_4'
      ],
      [
        'lastname' => 'Magassa',
        'firstname' => 'Soungoutou',
        'position' => 'Défenseur',
        'image' => 'magassa.jpg',
        'team' => 'team_4'
      ],
      [
        'lastname' => 'Henrique',
        'firstname' => 'Caio',
        'position' => 'Défenseur',
        'image' => 'henrique.jpeg',
        'team' => 'team_4'
      ],
      [
        'lastname' => 'Fofana',
        'firstname' => 'Youssouf',
        'position' => 'Milieu',
        'image' => 'fofana.jpg',
        'team' => 'team_4'
      ],
      [
        'lastname' => 'Camara',
        'firstname' => 'Mohamed',
        'position' => 'Milieu',
        'image' => 'camara.png',
        'team' => 'team_4'
      ],
      [
        'lastname' => 'Golovin',
        'firstname' => 'Aleksandr',
        'position' => 'Milieu',
        'image' => 'golovin.jpg',
        'team' => 'team_4'
      ],
      [
        'lastname' => 'Ben Yedder',
        'firstname' => 'Wissam',
        'position' => 'Attaquant',
        'image' => 'benyedder.jpg',
        'team' => 'team_4'
      ],
      [
        'lastname' => 'Balogun',
        'firstname' => 'Folarin',
        'position' => 'Attaquant',
        'image' => 'balogun.jpg',
        'team' => 'team_4'
      ],
      [
        'lastname' => 'Minamino',
        'firstname' => 'Takumi',
        'position' => 'Attaquant',
        'image' => 'minamino.jpg',
        'team' => 'team_4'
      ]
    ];

    foreach ($data_player as $key => $value)
    {
      $player = new Player();
      $player->setLastname($value['lastname'])
        ->setFirstname($value['firstname'])
        ->setTeam($this->getReference($value['team']))
        ->setPosition($value['position'])
        ->setImagePath($value['image']);
      $manager->persist($player);
    }
  }
}
