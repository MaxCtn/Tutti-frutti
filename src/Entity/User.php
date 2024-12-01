<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Entité représentant un utilisateur de l'application.
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identifiant unique de l'utilisateur.
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
     * Adresse e-mail unique de l'utilisateur.
     *
     * @var string|null
     *
     * @ORM\Column(length=180, unique=true)
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * Rôles de l'utilisateur.
     *
     * @var array
     *
     * @ORM\Column
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe hashé de l'utilisateur.
     *
     * @var string|null
     *
     * @ORM\Column
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * URL de la photo de profil de l'utilisateur.
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $profilePicture = null;

    /**
     * Collection des albums favoris associés à l'utilisateur.
     *
     * @var Collection<int, FavoriteAlbum>
     *
     * @ORM\OneToMany(mappedBy="user", targetEntity=FavoriteAlbum::class, cascade={"persist"})
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: FavoriteAlbum::class, cascade: ['persist'])]
    private Collection $favoriteAlbums;

    /**
     * Constructeur de la classe User.
     * Initialise la collection des albums favoris.
     */
    public function __construct()
    {
        $this->favoriteAlbums = new ArrayCollection();
    }

    /**
     * Obtient l'identifiant unique de l'utilisateur.
     *
     * @return int|null L'identifiant de l'utilisateur.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtient l'adresse e-mail de l'utilisateur.
     *
     * @return string|null L'adresse e-mail.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse e-mail de l'utilisateur.
     *
     * @param string $email L'adresse e-mail.
     *
     * @return self
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Obtient l'identifiant unique de l'utilisateur (utilisé par le composant de sécurité).
     *
     * @return string L'identifiant unique (adresse e-mail).
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Obtient les rôles de l'utilisateur.
     *
     * @return array Les rôles uniques de l'utilisateur.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur.
     *
     * @param array $roles Les rôles de l'utilisateur.
     *
     * @return self
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Obtient le mot de passe hashé de l'utilisateur.
     *
     * @return string Le mot de passe hashé.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe hashé de l'utilisateur.
     *
     * @param string $password Le mot de passe hashé.
     *
     * @return self
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Efface les données sensibles de l'utilisateur (non utilisé ici).
     */
    public function eraseCredentials(): void
    {
        // Implémentation vide pour le composant de sécurité.
    }

    /**
     * Obtient la collection des albums favoris de l'utilisateur.
     *
     * @return Collection<int, FavoriteAlbum> La collection des albums favoris.
     */
    public function getFavoriteAlbums(): Collection
    {
        return $this->favoriteAlbums;
    }

    /**
     * Ajoute un album favori à la collection de l'utilisateur.
     *
     * @param FavoriteAlbum $favoriteAlbum L'album favori à ajouter.
     *
     * @return self
     */
    public function addFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if (!$this->favoriteAlbums->contains($favoriteAlbum)) {
            $this->favoriteAlbums[] = $favoriteAlbum;
            $favoriteAlbum->setUser($this);
        }

        return $this;
    }

    /**
     * Retire un album favori de la collection de l'utilisateur.
     *
     * @param FavoriteAlbum $favoriteAlbum L'album favori à retirer.
     *
     * @return self
     */
    public function removeFavoriteAlbum(FavoriteAlbum $favoriteAlbum): self
    {
        if ($this->favoriteAlbums->removeElement($favoriteAlbum)) {
            if ($favoriteAlbum->getUser() === $this) {
                $favoriteAlbum->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Obtient l'URL de la photo de profil de l'utilisateur.
     *
     * @return string|null L'URL de la photo de profil.
     */
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    /**
     * Définit l'URL de la photo de profil de l'utilisateur.
     *
     * @param string|null $profilePicture L'URL de la photo de profil.
     *
     * @return self
     */
    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }
}
