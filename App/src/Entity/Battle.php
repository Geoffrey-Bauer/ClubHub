<?php

namespace App\Entity;

use App\Repository\BattleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BattleRepository::class)]
class Battle
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $date = null;

  #[ORM\Column(length: 255)]
  private ?string $lieu = null;

  #[ORM\Column(nullable: true, )]
  private ?int $scoreDomicile = 0;

  #[ORM\Column(nullable: true)]
  private ?int $scoreExterieur = 0;

  #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'homeBattles')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Team $teamDomicile = null;

  #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'awayBattles')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Team $teamExterieur = null;


  public function getId(): ?int
  {
    return $this->id;
  }

  public function getDate(): ?\DateTimeInterface
  {
    return $this->date;
  }

  public function setDate(\DateTimeInterface $date): static
  {
    $this->date = $date;

    return $this;
  }

  public function getLieu(): ?string
  {
    return $this->lieu;
  }

  public function setLieu(string $lieu): static
  {
    $this->lieu = $lieu;

    return $this;
  }

  public function getScoreDomicile(): ?int
  {
    return $this->scoreDomicile;
  }

  public function setScoreDomicile(?int $scoreDomicile): static
  {
    $this->scoreDomicile = $scoreDomicile;

    return $this;
  }

  public function getScoreExterieur(): ?int
  {
    return $this->scoreExterieur;
  }

  public function setScoreExterieur(?int $scoreExterieur): static
  {
    $this->scoreExterieur = $scoreExterieur;

    return $this;
  }

  public function getTeamDomicile(): ?Team
  {
    return $this->teamDomicile;
  }

  public function setTeamDomicile(?Team $teamDomicile): static
  {
    $this->teamDomicile = $teamDomicile;

    return $this;
  }

  public function getTeamExterieur(): ?Team
  {
    return $this->teamExterieur;
  }

  public function setTeamExterieur(?Team $teamExterieur): static
  {
    $this->teamExterieur = $teamExterieur;

    return $this;
  }

  public function updateScore(Player $player, int $oldGoal, int $newGoal): void
  {
    if ($player->getTeam() === $this->getTeamDomicile()) {
      $this->setScoreDomicile($this->getScoreDomicile() - $oldGoal + $newGoal);
    } else {
      $this->setScoreExterieur($this->getScoreExterieur() - $oldGoal + $newGoal);
    }
  }

  public function __toString()
  {
    return (string) $this->id;
  }
}