<?php

namespace App\Entity;

use App\Repository\FruitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un fruit associé à des albums favoris.
 *
 * @ORM\Entity(repositoryClass=FruitRepository::class)
 */
#[ORM\Entity(repositoryClass: FruitRepository::class)]
class Fruit
{
    /**
     * Identifiant unique du fruit.
     *
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom unique du fruit (par exemple, "Banane", "Fraise").
     *
     * @var string|null
     *
     * @ORM\Column(length=255, unique=true)
     */
    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    /**
     * Collection des albums favoris associés à ce fruit.
     *
     * @var Collection<int, FavoriteAlbum>
     *
     * @ORM\ManyToMany(mappedBy="fruits", targetEntity=FavoriteAlbum::class)
     */
    #[ORM\ManyToMany(mappedBy: 'fruits', targetEntity: FavoriteAlbum::class)]
    private Collection $albums;

    /**
     * Constructeur de la classe Fruit.
     * Initialise la collection des albums favoris.
     */
    public function __construct()
    {
        $this->albums = new ArrayCollection();
    }

    /**
     * Obtient l'identifiant unique du fruit.
     *
     * @return int|null L'identifiant du fruit.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtient le nom du fruit.
     *
     * @return string|null Le nom du fruit.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom du fruit.
     *
     * @param string $name Le nom du fruit.
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Obtient la collection des albums favoris associés à ce fruit.
     *
     * @return Collection<int, FavoriteAlbum> La collection des albums favoris.
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    /**
     * Ajoute un album favori à la collection associée à ce fruit.
     *
     * @param FavoriteAlbum $album L'album favori à ajouter.
     *
     * @return self
     */
    public function addAlbum(FavoriteAlbum $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->addFruit($this);
        }

        return $this;
    }

    /**
     * Retire un album favori de la collection associée à ce fruit.
     *
     * @param FavoriteAlbum $album L'album favori à retirer.
     *
     * @return self
     */
    public function removeAlbum(FavoriteAlbum $album): self
    {
        if ($this->albums->removeElement($album)) {
            $album->removeFruit($this);
        }

        return $this;
    }
}
