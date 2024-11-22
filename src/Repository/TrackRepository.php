<?php
#[ORM\Entity(repositoryClass: TrackRepository::class)]
class Track
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $duration = null;

    #[ORM\ManyToOne(targetEntity: FavoriteAlbum::class, inversedBy: 'tracks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FavoriteAlbum $album = null;

    #[ORM\ManyToMany(targetEntity: Fruit::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name: 'track_fruit')]
    private Collection $fruits;

    public function __construct()
    {
        $this->fruits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getAlbum(): ?FavoriteAlbum
    {
        return $this->album;
    }

    public function setAlbum(?FavoriteAlbum $album): self
    {
        $this->album = $album;

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
            $this->fruits->add($fruit);
        }

        return $this;
    }

    public function removeFruit(Fruit $fruit): self
    {
        $this->fruits->removeElement($fruit);

        return $this;
    }
}
