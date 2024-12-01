<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\FormatRepository;

/**
 * Entité représentant un format (par exemple, vinyle, CD).
 *
 * @ORM\Entity(repositoryClass=FormatRepository::class)
 */
#[ORM\Entity(repositoryClass: FormatRepository::class)]
class Format
{
    /**
     * Identifiant unique du format.
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
     * Nom du format (par exemple, "Vinyle", "CD").
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    /**
     * Collection des albums favoris associés à ce format.
     *
     * @var Collection<int, FavoriteAlbum>
     *
     * @ORM\OneToMany(mappedBy="format", targetEntity=FavoriteAlbum::class)
     */
    #[ORM\OneToMany(mappedBy: 'format', targetEntity: FavoriteAlbum::class)]
    private Collection $favoriteAlbums;

    /**
     * Constructeur de la classe Format.
     * Initialise la collection des albums favoris.
     */
    public function __construct()
    {
        $this->favoriteAlbums = new ArrayCollection();
    }

    /**
     * Obtient l'identifiant unique du format.
     *
     * @return int|null L'identifiant du format.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtient le nom du format.
     *
     * @return string|null Le nom du format.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du format.
     *
     * @param string $name Le nom du format.
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Obtient la collection des albums favoris associés à ce format.
     *
     * @return Collection<int, FavoriteAlbum> La collection des albums favoris.
     */
    public function getFavoriteAlbums(): Collection
    {
        return $this->favoriteAlbums;
    }

    /**
     * Ajoute un album favori à la collection associée.
     *
     * @param FavoriteAlbum $favoriteAlbum L'album favori à ajouter.
     *
     * @return self
     */
    public function addFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if (!$this->favoriteAlbums->contains($favoriteAlbum)) {
            $this->favoriteAlbums[] = $favoriteAlbum;
            $favoriteAlbum->setFormat($this);
        }

        return $this;
    }

    /**
     * Retire un album favori de la collection associée.
     *
     * @param FavoriteAlbum $favoriteAlbum L'album favori à retirer.
     *
     * @return self
     */
    public function removeFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if ($this->favoriteAlbums->removeElement($favoriteAlbum)) {
            // Définir le côté propriétaire de la relation à null
            if ($favoriteAlbum->getFormat() === $this) {
                $favoriteAlbum->setFormat(null);
            }
        }

        return $this;
    }
}
