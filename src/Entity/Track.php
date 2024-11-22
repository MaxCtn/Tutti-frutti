<?php

namespace App\Entity;

use App\Repository\TrackRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
}
