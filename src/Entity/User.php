<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: FavoriteAlbum::class, cascade: ['persist'])]
    private $favoriteAlbums;

    public function __construct()
    {
        $this->favoriteAlbums = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getFavoriteAlbums(): Collection
    {
        return $this->favoriteAlbums;
    }

    public function addFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if (!$this->favoriteAlbums->contains($favoriteAlbum)) {
            $this->favoriteAlbums[] = $favoriteAlbum;
            $favoriteAlbum->setUser($this);
        }

        return $this;
    }

    public function removeFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if ($this->favoriteAlbums->removeElement($favoriteAlbum)) {
            if ($favoriteAlbum->getUser() === $this) {
                $favoriteAlbum->setUser(null);
            }
        }

        return $this;
    }

    // Getter et setter pour profilePicture
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }
}
