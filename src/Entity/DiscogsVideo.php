<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DiscogsVideo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 255)]
    private string $url;

    #[ORM\ManyToOne(targetEntity: DiscogsMaster::class, inversedBy: 'videos')]
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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
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
