<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\GenreRepository;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
    
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'genre', targetEntity: FavoriteAlbum::class)]
    private Collection $favoriteAlbums;

    public function __construct()
    {
        $this->favoriteAlbums = new ArrayCollection();
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
     * @return Collection|FavoriteAlbum[]
     */
    public function getFavoriteAlbums(): Collection
    {
        return $this->favoriteAlbums;
    }

    public function addFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if (!$this->favoriteAlbums->contains($favoriteAlbum)) {
            $this->favoriteAlbums[] = $favoriteAlbum;
            $favoriteAlbum->setGenre($this);
        }

        return $this;
    }

    public function removeFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if ($this->favoriteAlbums->removeElement($favoriteAlbum)) {
            // set the owning side to null (unless already changed)
            if ($favoriteAlbum->getGenre() === $this) {
                $favoriteAlbum->setGenre(null);
                }
        }
                return $this;
    }
}