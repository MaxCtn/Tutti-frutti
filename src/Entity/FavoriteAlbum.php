<?php

namespace App\Entity;

use App\Repository\FavoriteAlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

/**
 * Entité représentant un album favori d'un utilisateur.
 *
 * @ORM\Entity(repositoryClass=FavoriteAlbumRepository::class)
 * @Broadcast
 */

#[ORM\Entity(repositoryClass: FavoriteAlbumRepository::class)]
#[Broadcast]
class FavoriteAlbum
{
    /**
     * Identifiant unique de l'album favori.
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
     * Identifiant de l'album dans le système externe (par exemple, Discogs).
     *
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: 'integer')]
    private ?int $albumId = null;

    /**
     * Titre de l'album.
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title = null;

    /**
     * Année de sortie de l'album.
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $year = null;

    /**
     * URL de l'image de couverture de l'album.
     * Si aucune URL n'est disponible, utilise une image par défaut.
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $coverImage = '/images/placeholder.jpg';

    /**
     * Label associé à l'album.
     *
     * @var Label|null
     *
     * @ORM\ManyToOne(targetEntity=Label::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    #[ORM\ManyToOne(targetEntity: Label::class, inversedBy: 'favoriteAlbums')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Label $label = null;


    /**
     * Genre musical de l'album.
     *
     * @var Genre|null
     *
     * @ORM\ManyToOne(targetEntity=Genre::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    #[ORM\ManyToOne(targetEntity: Genre::class, inversedBy: 'favoriteAlbums')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Genre $genre = null;


    /**
     * Format de l'album (vinyle, CD, etc.).
     *
     * @var Format|null
     *
     * @ORM\ManyToOne(targetEntity=Format::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    #[ORM\ManyToOne(targetEntity: Format::class, inversedBy: 'favoriteAlbums')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Format $format = null;


    /**
     * Utilisateur propriétaire de l'album favori.
     *
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="favoriteAlbums")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'favoriteAlbums')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    /**
     * Collection des fruits associés à l'album.
     *
     * @var Collection<int, Fruit>
     *
     * @ORM\ManyToMany(targetEntity=Fruit::class, cascade={"persist", "remove"})
     * @ORM\JoinTable(name="favorite_album_fruit")
     */
    #[ORM\ManyToMany(targetEntity: Fruit::class, inversedBy: 'albums', cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name: 'favorite_album_fruit')]
    private Collection $fruits;


    /**
     * Collection des pistes (tracks) associées à l'album.
     *
     * @var Collection<int, Track>
     *
     * @ORM\OneToMany(mappedBy="album", targetEntity=Track::class, cascade={"persist", "remove"})
     */
    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Track::class, cascade: ['persist', 'remove'])]
    private Collection $tracks;

    /**
     * Constructeur de la classe FavoriteAlbum.
     * Initialise les collections de fruits et de pistes.
     */
    public function __construct()
    {
        $this->fruits = new ArrayCollection();
        $this->tracks = new ArrayCollection();
    }

    // Getters et setters avec documentation

    /**
     * Obtient l'identifiant unique de l'album favori.
     *
     * @return int|null L'identifiant de l'album favori.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtient l'identifiant de l'album dans le système externe.
     *
     * @return int|null L'identifiant de l'album.
     */
    public function getAlbumId(): ?int
    {
        return $this->albumId;
    }

    /**
     * Définit l'identifiant de l'album dans le système externe.
     *
     * @param int $albumId L'identifiant de l'album.
     *
     * @return self
     */
    public function setAlbumId(int $albumId): self
    {
        $this->albumId = $albumId;
        return $this;
    }

    /**
     * Obtient le titre de l'album.
     *
     * @return string|null Le titre de l'album.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre de l'album.
     *
     * @param string|null $title Le titre de l'album.
     *
     * @return self
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Obtient l'année de sortie de l'album.
     *
     * @return string|null L'année de sortie.
     */
    public function getYear(): ?string
    {
        return $this->year;
    }

    /**
     * Définit l'année de sortie de l'album.
     *
     * @param string|null $year L'année de sortie.
     *
     * @return self
     */
    public function setYear(?string $year): self
    {
        $this->year = $year;
        return $this;
    }

    /**
     * Obtient l'URL de l'image de couverture.
     * Si aucune URL n'est définie, retourne une image par défaut.
     *
     * @return string|null L'URL de l'image de couverture.
     */
    public function getCoverImage(): ?string
    {
        return $this->coverImage ?? '/images/placeholder.jpg';
    }

    /**
     * Définit l'URL de l'image de couverture.
     *
     * @param string|null $coverImage L'URL de l'image.
     *
     * @return self
     */
    public function setCoverImage(?string $coverImage): self
    {
        $this->coverImage = $coverImage;
        return $this;
    }

    /**
     * Obtient le label de l'album.
     *
     * @return Label|null Le label de l'album.
     */
    public function getLabel(): ?Label
    {
        return $this->label;
    }

    /**
     * Définit le label de l'album.
     *
     * @param Label|null $label Le label.
     *
     * @return self
     */
    public function setLabel(?Label $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Obtient le genre musical de l'album.
     *
     * @return Genre|null Le genre musical.
     */
    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    /**
     * Définit le genre musical de l'album.
     *
     * @param Genre|null $genre Le genre musical.
     *
     * @return self
     */
    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;
        return $this;
    }

    /**
     * Obtient le format de l'album (vinyle, CD, etc.).
     *
     * @return Format|null Le format de l'album.
     */
    public function getFormat(): ?Format
    {
        return $this->format;
    }

    /**
     * Définit le format de l'album.
     *
     * @param Format|null $format Le format.
     *
     * @return self
     */
    public function setFormat(?Format $format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Obtient l'utilisateur propriétaire de l'album favori.
     *
     * @return User|null L'utilisateur.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Définit l'utilisateur propriétaire de l'album favori.
     *
     * @param User|null $user L'utilisateur.
     *
     * @return self
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Obtient la collection des fruits associés à l'album.
     *
     * @return Collection<int, Fruit> La collection de fruits.
     */
    public function getFruits(): Collection
    {
        return $this->fruits;
    }

    /**
     * Ajoute un fruit à la collection des fruits associés.
     *
     * @param Fruit $fruit Le fruit à ajouter.
     *
     * @return self
     */
    public function addFruit(Fruit $fruit): self
    {
        if (!$this->fruits->contains($fruit)) {
            $this->fruits[] = $fruit;
        }
        return $this;
    }

    /**
     * Retire un fruit de la collection des fruits associés.
     *
     * @param Fruit $fruit Le fruit à retirer.
     *
     * @return self
     */
    public function removeFruit(Fruit $fruit): self
    {
        $this->fruits->removeElement($fruit);
        return $this;
    }

    /**
     * Obtient la collection des pistes associées à l'album.
     *
     * @return Collection<int, Track> La collection de pistes.
     */
    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    /**
     * Ajoute une piste à la collection des pistes associées.
     *
     * @param Track $track La piste à ajouter.
     *
     * @return self
     */
    public function addTrack(Track $track): self
    {
        if (!$this->tracks->contains($track)) {
            $this->tracks[] = $track;
            $track->setAlbum($this);
        }
        return $this;
    }

    /**
     * Retire une piste de la collection des pistes associées.
     *
     * @param Track $track La piste à retirer.
     *
     * @return self
     */
    public function removeTrack(Track $track): self
    {
        if ($this->tracks->removeElement($track)) {
            // Définir le côté propriétaire de la relation à null
            if ($track->getAlbum() === $this) {
                $track->setAlbum(null);
            }
        }
        return $this;
    }

    /**
     * Récupère tous les fruits associés à l'album et à ses morceaux.
     *
     * @return array La liste de tous les fruits associés.
     */
    public function getAllFruits(): array
    {
        $allFruits = $this->fruits->toArray();

        foreach ($this->tracks as $track) {
            foreach ($track->getFruits() as $fruit) {
                if (!in_array($fruit, $allFruits, true)) {
                    $allFruits[] = $fruit;
                }
            }
        }

        return $allFruits;
    }
}
