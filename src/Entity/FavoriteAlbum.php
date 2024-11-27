<?php

namespace App\Entity;

use App\Repository\FavoriteAlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: FavoriteAlbumRepository::class)]
#[Broadcast]
class FavoriteAlbum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $albumId = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $year = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $coverImage = null;

    #[ORM\ManyToOne(targetEntity: Label::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Label $label = null;

    #[ORM\ManyToOne(targetEntity: Genre::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Genre $genre = null;

    #[ORM\ManyToOne(targetEntity: Format::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Format $format = null;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'favoriteAlbums')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Fruit::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name: 'favorite_album_fruit')]
    private Collection $fruits;

    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Track::class, cascade: ['persist', 'remove'])]
    private Collection $tracks;

    public function __construct()
    {
        $this->fruits = new ArrayCollection();
        $this->tracks = new ArrayCollection();
    }

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlbumId(): ?int
    {
        return $this->albumId;
    }

    public function setAlbumId(int $albumId): self
    {
        $this->albumId = $albumId;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $coverImage): self
    {
        $this->coverImage = $coverImage;
        return $this;
    }

    public function getLabel(): ?Label
    {
        return $this->label;
    }

    public function setLabel(?Label $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;
        return $this;
    }

    public function getFormat(): ?Format
    {
        return $this->format;
    }

    public function setFormat(?Format $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<int, Fruit>
     */
    public function getFruits(): Collection
    {
        return $this->fruits;
    }

    public function addFruit(Fruit $fruit): self
    {
        if (!$this->fruits->contains($fruit)) {
            $this->fruits[] = $fruit;
        }
        return $this;
    }

    public function removeFruit(Fruit $fruit): self
    {
        $this->fruits->removeElement($fruit);
        return $this;
    }

    /**
     * @return Collection<int, Track>
     */
    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function addTrack(Track $track): self
    {
        if (!$this->tracks->contains($track)) {
            $this->tracks[] = $track;
            $track->setAlbum($this);
        }
        return $this;
    }

    public function removeTrack(Track $track): self
    {
        if ($this->tracks->removeElement($track)) {
            if ($track->getAlbum() === $this) {
                $track->setAlbum(null);
            }
        }
        return $this;
    }

    /**
     * Récupère tous les fruits associés à l'album et à ses morceaux.
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
