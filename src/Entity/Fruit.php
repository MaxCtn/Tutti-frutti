<?php

namespace App\Entity;

use App\Repository\FruitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FruitRepository::class)]
class Fruit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\ManyToMany(mappedBy: 'fruits', targetEntity: FavoriteAlbum::class)]
    private Collection $albums;

    public function __construct()
    {
        $this->albums = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, FavoriteAlbum>
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(FavoriteAlbum $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->addFruit($this);
        }

        return $this;
    }

    public function removeAlbum(FavoriteAlbum $album): self
    {
        if ($this->albums->removeElement($album)) {
            $album->removeFruit($this);
        }

        return $this;
    }
}
