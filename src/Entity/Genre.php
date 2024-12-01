<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\GenreRepository;

/**
 * Entité représentant un genre musical associé à des albums favoris.
 *
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 */
#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    /**
     * Identifiant unique du genre.
     *
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Nom du genre musical (par exemple, "Rock", "Jazz").
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    /**
     * Collection des albums favoris associés à ce genre.
     *
     * @var Collection<int, FavoriteAlbum>
     *
     * @ORM\OneToMany(mappedBy="genre", targetEntity=FavoriteAlbum::class)
     */
    #[ORM\OneToMany(mappedBy: 'genre', targetEntity: FavoriteAlbum::class)]
    private Collection $favoriteAlbums;

    /**
     * Constructeur de la classe Genre.
     * Initialise la collection des albums favoris.
     */
    public function __construct()
    {
        $this->favoriteAlbums = new ArrayCollection();
    }

    /**
     * Obtient l'identifiant unique du genre.
     *
     * @return int|null L'identifiant du genre.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtient le nom du genre musical.
     *
     * @return string|null Le nom du genre.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du genre musical.
     *
     * @param string $name Le nom du genre.
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Obtient la collection des albums favoris associés à ce genre.
     *
     * @return Collection<int, FavoriteAlbum> La collection des albums favoris.
     */
    public function getFavoriteAlbums(): Collection
    {
        return $this->favoriteAlbums;
    }

    /**
     * Ajoute un album favori à la collection associée à ce genre.
     *
     * @param FavoriteAlbum $favoriteAlbum L'album favori à ajouter.
     *
     * @return self
     */
    public function addFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if (!$this->favoriteAlbums->contains($favoriteAlbum)) {
            $this->favoriteAlbums[] = $favoriteAlbum;
            $favoriteAlbum->setGenre($this);
        }

        return $this;
    }

    /**
     * Retire un album favori de la collection associée à ce genre.
     *
     * @param FavoriteAlbum $favoriteAlbum L'album favori à retirer.
     *
     * @return self
     */
    public function removeFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if ($this->favoriteAlbums->removeElement($favoriteAlbum)) {
            // Définir le côté propriétaire de la relation à null
            if ($favoriteAlbum->getGenre() === $this) {
                $favoriteAlbum->setGenre(null);
            }
        }
        return $this;
    }
}
