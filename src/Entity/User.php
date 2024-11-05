<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $username;

    #[ORM\Column(type: 'string', length: 100)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Favorite::class, cascade: ['persist', 'remove'])]
    private Collection $favorites;

    public function __construct()
    {
        $this->favorites = new ArrayCollection();
    }

    // Getters et Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return Collection|Favorite[]
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setUser($this);
        }
        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // Set the owning side to null (unless already changed)
            if ($favorite->getUser() === $this) {
                $favorite->setUser(null);
            }
        }
        return $this;
    }
}
