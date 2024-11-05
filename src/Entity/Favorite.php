<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: DiscogsTrack::class)]
    #[ORM\JoinColumn(nullable: false)]
    private DiscogsTrack $track;

    // Getters et Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTrack(): DiscogsTrack
    {
        return $this->track;
    }

    public function setTrack(DiscogsTrack $track): self
    {
        $this->track = $track;
        return $this;
    }
}
