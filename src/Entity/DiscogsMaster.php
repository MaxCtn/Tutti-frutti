<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class DiscogsMaster
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 4, nullable: true)]
    private ?string $year = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $genre = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $format = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $fruitKeyword = null;

    #[ORM\ManyToOne(targetEntity: DiscogsArtist::class, inversedBy: 'masters')]
    #[ORM\JoinColumn(nullable: false)]
    private DiscogsArtist $artist;

    #[ORM\OneToMany(mappedBy: 'master', targetEntity: DiscogsTrack::class, cascade: ['persist', 'remove'])]
    private Collection $tracks;

    #[ORM\OneToMany(mappedBy: 'master', targetEntity: DiscogsVideo::class, cascade: ['persist', 'remove'])]
    private Collection $videos;

    public function __construct()
    {
        $this->tracks = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    // Getters et Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
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

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function getFruitKeyword(): ?string
    {
        return $this->fruitKeyword;
    }

    public function setFruitKeyword(?string $fruitKeyword): self
    {
        $this->fruitKeyword = $fruitKeyword;
        return $this;
    }

    public function getArtist(): DiscogsArtist
    {
        return $this->artist;
    }

    public function setArtist(DiscogsArtist $artist): self
    {
        $this->artist = $artist;
        return $this;
    }

    /**
     * @return Collection|DiscogsTrack[]
     */
    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function addTrack(DiscogsTrack $track): self
    {
        if (!$this->tracks->contains($track)) {
            $this->tracks[] = $track;
            $track->setMaster($this);
        }
        return $this;
    }

    public function removeTrack(DiscogsTrack $track): self
    {
        if ($this->tracks->removeElement($track)) {
            // Set the owning side to null (unless already changed)
            if ($track->getMaster() === $this) {
                $track->setMaster(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|DiscogsVideo[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(DiscogsVideo $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setMaster($this);
        }
        return $this;
    }

    public function removeVideo(DiscogsVideo $video): self
    {
        if ($this->videos->removeElement($video)) {
            // Set the owning side to null (unless already changed)
            if ($video->getMaster() === $this) {
                $video->setMaster(null);
            }
        }
        return $this;
    }
}
