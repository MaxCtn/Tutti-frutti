<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\LabelRepository;

/**
 * Entité représentant un label musical associé à des albums favoris.
 *
 * @ORM\Entity(repositoryClass=LabelRepository::class)
 */
#[ORM\Entity(repositoryClass: LabelRepository::class)]
class Label
{
    /**
     * Identifiant unique du label.
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
     * Nom du label musical (par exemple, "Sony Music", "EMI").
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    /**
     * Collection des albums favoris associés à ce label.
     *
     * @var Collection<int, FavoriteAlbum>
     *
     * @ORM\OneToMany(mappedBy="label", targetEntity=FavoriteAlbum::class)
     */
    #[ORM\OneToMany(mappedBy: 'label', targetEntity: FavoriteAlbum::class)]
    private Collection $favoriteAlbums;


    /**
     * Constructeur de la classe Label.
     * Initialise la collection des albums favoris.
     */
    public function __construct()
    {
        $this->favoriteAlbums = new ArrayCollection();
    }

    /**
     * Obtient l'identifiant unique du label.
     *
     * @return int|null L'identifiant du label.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtient le nom du label musical.
     *
     * @return string|null Le nom du label.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du label musical.
     *
     * @param string $name Le nom du label.
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Obtient la collection des albums favoris associés à ce label.
     *
     * @return Collection<int, FavoriteAlbum> La collection des albums favoris.
     */
    public function getFavoriteAlbums(): Collection
    {
        return $this->favoriteAlbums;
    }

    /**
     * Ajoute un album favori à la collection associée à ce label.
     *
     * @param FavoriteAlbum $favoriteAlbum L'album favori à ajouter.
     *
     * @return self
     */
    public function addFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if (!$this->favoriteAlbums->contains($favoriteAlbum)) {
            $this->favoriteAlbums[] = $favoriteAlbum;
            $favoriteAlbum->setLabel($this);
        }

        return $this;
    }

    /**
     * Retire un album favori de la collection associée à ce label.
     *
     * @param FavoriteAlbum $favoriteAlbum L'album favori à retirer.
     *
     * @return self
     */
    public function removeFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if ($this->favoriteAlbums->removeElement($favoriteAlbum)) {
            // Définir le côté propriétaire de la relation à null
            if ($favoriteAlbum->getLabel() === $this) {
                $favoriteAlbum->setLabel(null);
            }
        }

        return $this;
    }
}
