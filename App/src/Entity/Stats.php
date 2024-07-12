<?php

namespace App\Entity;

use App\Repository\StatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatsRepository::class)]
class Stats
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(nullable: true)]
  private ?int $goal = 0;

  #[ORM\Column(nullable: true)]
  private ?int $assists = 0;

  #[ORM\Column(nullable: true)]
  private ?int $yellow_card = 0;

  #[ORM\Column(nullable: true)]
  private ?int $red_card = 0;

  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?Player $player = null;

  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?Battle $battle = null;

  #[ORM\Column(length: 50, nullable: true)]
  private ?string $time = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getGoal(): ?int
  {
    return $this->goal;
  }

  public function setGoal(?int $goal): static
  {
    $this->goal = $goal;

    return $this;
  }

  public function getAssists(): ?int
  {
    return $this->assists;
  }

  public function setAssists(?int $assists): static
  {
    $this->assists = $assists;

    return $this;
  }

  public function getYellowCard(): ?int
  {
    return $this->yellow_card;
  }

  public function setYellowCard(?int $yellow_card): static
  {
    $this->yellow_card = $yellow_card;

    return $this;
  }

  public function getRedCard(): ?int
  {
    return $this->red_card;
  }

  public function setRedCard(?int $red_card): static
  {
    $this->red_card = $red_card;

    return $this;
  }

  public function getPlayer(): ?Player
  {
    return $this->player;
  }

  public function setPlayer(?Player $player): static
  {
    $this->player = $player;

    return $this;
  }

  public function getBattle(): ?Battle
  {
    return $this->battle;
  }

  public function setBattle(?Battle $battle): static
  {
    $this->battle = $battle;

    return $this;
  }

  public function getTime(): ?string
  {
    return $this->time;
  }

  public function setTime(?string $time): static
  {
    $this->time = $time;

    return $this;
  }
}
