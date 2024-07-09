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

  #[ORM\Column]
  private ?int $goal = null;

  #[ORM\Column]
  private ?int $assists = null;

  #[ORM\Column]
  private ?int $yellow_card = null;

  #[ORM\Column]
  private ?int $red_card = null;

  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?Player $player = null;

  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?Battle $battle = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getGoal(): ?int
  {
    return $this->goal;
  }

  public function setGoal(int $goal): static
  {
    $this->goal = $goal;

    return $this;
  }

  public function getAssists(): ?int
  {
    return $this->assists;
  }

  public function setAssists(int $assists): static
  {
    $this->assists = $assists;

    return $this;
  }

  public function getYellowCard(): ?int
  {
    return $this->yellow_card;
  }

  public function setYellowCard(int $yellow_card): static
  {
    $this->yellow_card = $yellow_card;

    return $this;
  }

  public function getRedCard(): ?int
  {
    return $this->red_card;
  }

  public function setRedCard(int $red_card): static
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
}
