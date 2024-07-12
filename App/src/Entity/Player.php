<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
#[ORM\Table(name: '`player`')]
#[Vich\Uploadable]
class Player
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $lastname = null;

  #[ORM\Column(length: 255)]
  private ?string $firstname = null;

  #[ORM\Column(length: 255)]
  private ?string $position = null;

  #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'players')]
  #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
  private ?Team $team = null;

  #[ORM\Column(nullable: true)]
  private ?bool $isCoach = null;

  #[Vich\UploadableField(mapping: 'player_images', fileNameProperty: 'imagePath')]
  private ?File $imageFile = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $imagePath = null;

  #[ORM\Column(type: 'datetime', nullable: true)]
  private ?\DateTimeInterface $updatedAt = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getLastname(): ?string
  {
    return $this->lastname;
  }

  public function setLastname(string $lastname): static
  {
    $this->lastname = $lastname;

    return $this;
  }

  public function getFirstname(): ?string
  {
    return $this->firstname;
  }

  public function setFirstname(string $firstname): static
  {
    $this->firstname = $firstname;

    return $this;
  }

  public function getPosition(): ?string
  {
    return $this->position;
  }

  public function setPosition(string $position): static
  {
    $this->position = $position;

    return $this;
  }

  public function getTeam(): ?Team
  {
    return $this->team;
  }

  public function setTeam(?Team $team): static
  {
    $this->team = $team;

    return $this;
  }

  public function getIsCoach(): ?bool
  {
    return $this->isCoach;
  }

  public function setIsCoach(bool $isCoach): static
  {
    $this->isCoach = $isCoach;

    return $this;
  }

  public function getImagePath(): ?string
  {
    return $this->imagePath;
  }

  public function setImagePath(?string $imagePath): static
  {
    $this->imagePath = $imagePath;

    return $this;
  }

  public function setImageFile(?File $imageFile = null): void
  {
    $this->imageFile = $imageFile;

    if (null !== $imageFile) {
      $this->updatedAt = new \DateTimeImmutable();
    }
  }

  public function getImageFile(): ?File
  {
    return $this->imageFile;
  }
}