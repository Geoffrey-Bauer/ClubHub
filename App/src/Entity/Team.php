<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[Vich\Uploadable]
class Team
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $name = null;

  #[ORM\ManyToOne]
  private ?Player $player = null;

  #[ORM\OneToMany(mappedBy: 'team', targetEntity: Player::class, cascade: ['persist', 'remove'])]
  private Collection $players;

  #[Vich\UploadableField(mapping: 'team_images', fileNameProperty: 'imagePath')]
  private ?File $imageFile = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $imagePath = null;

  #[ORM\Column(type: 'datetime', nullable: true)]
  private ?\DateTimeInterface $updatedAt = null;

  public function __construct()
  {
    $this->players = new ArrayCollection();
  }

  /**
   * @return Collection<int, Player>
   */
  public function getPlayers(): Collection
  {
    return $this->players;
  }

  public function addPlayer(Player $player): self
  {
    if (!$this->players->contains($player)) {
      $this->players->add($player);
      $player->setTeam($this);
    }

    return $this;
  }

  public function removePlayer(Player $player): self
  {
    if ($this->players->removeElement($player)) {
      // set the owning side to null (unless already changed)
      if ($player->getTeam() === $this) {
        $player->setTeam(null);
      }
    }

    return $this;
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): static
  {
    $this->name = $name;

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

  public function setImagePath(?string $imagePath): void
  {
    $this->imagePath = $imagePath;
  }

  public function getImagePath(): ?string
  {
    return $this->imagePath;
  }

  public function getUpdatedAt(): ?\DateTimeInterface
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }
}