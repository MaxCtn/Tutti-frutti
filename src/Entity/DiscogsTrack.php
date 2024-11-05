<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DiscogsTrack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $duration = null;

    #[ORM\ManyToOne(targetEntity: DiscogsMaster::class, inversedBy: 'tracks')]
    #[ORM\JoinColumn(nullable: false)]
    private DiscogsMaster $master;

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

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    public function getMaster(): DiscogsMaster
    {
        return $this->master;
    }

    public function setMaster(DiscogsMaster $master): self
    {
        $this->master = $master;
        return $this;
    }
}
