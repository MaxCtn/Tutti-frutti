<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class DiscogsArtist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $genre = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $profile = null;

    #[ORM\OneToMany(mappedBy: 'artist', targetEntity: DiscogsMaster::class)]
    private Collection $masters;

    public function __construct()
    {
        $this->masters = new ArrayCollection();
    }

    // Getters et Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(?string $profile): self
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * @return Collection|DiscogsMaster[]
     */
    public function getMasters(): Collection
    {
        return $this->masters;
    }

    public function addMaster(DiscogsMaster $master): self
    {
        if (!$this->masters->contains($master)) {
            $this->masters[] = $master;
            $master->setArtist($this);
        }
        return $this;
    }

    public function removeMaster(DiscogsMaster $master): self
    {
        if ($this->masters->removeElement($master)) {
            // Set the owning side to null (unless already changed)
            if ($master->getArtist() === $this) {
                $master->setArtist(null);
            }
        }
        return $this;
    }
}
