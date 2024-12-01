<?php

namespace App\Entity;

use App\Repository\TrackRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une piste (track) d'un album favori.
 *
 * @ORM\Entity(repositoryClass=TrackRepository::class)
 */
#[ORM\Entity(repositoryClass: TrackRepository::class)]
class Track
{
    /**
     * Identifiant unique de la piste.
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
     * Titre de la piste.
     *
     * @var string|null
     *
     * @ORM\Column(length=255)
     */
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * Durée de la piste au format "mm:ss".
     *
     * @var string|null
     *
     * @ORM\Column(length=10, nullable=true)
     */
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $duration = null;

    /**
     * Album favori auquel appartient la piste.
     *
     * @var FavoriteAlbum|null
     *
     * @ORM\ManyToOne(targetEntity=FavoriteAlbum::class, inversedBy="tracks")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: FavoriteAlbum::class, inversedBy: 'tracks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FavoriteAlbum $album = null;

    /**
     * Obtient l'identifiant unique de la piste.
     *
     * @return int|null L'identifiant de la piste.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtient le titre de la piste.
     *
     * @return string|null Le titre de la piste.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre de la piste.
     *
     * @param string $title Le titre de la piste.
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Obtient la durée de la piste au format "mm:ss".
     *
     * @return string|null La durée de la piste.
     */
    public function getDuration(): ?string
    {
        return $this->duration;
    }

    /**
     * Définit la durée de la piste au format "mm:ss".
     *
     * @param string|null $duration La durée de la piste.
     *
     * @return self
     */
    public function setDuration(?string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Obtient l'album favori auquel appartient la piste.
     *
     * @return FavoriteAlbum|null L'album favori.
     */
    public function getAlbum(): ?FavoriteAlbum
    {
        return $this->album;
    }

    /**
     * Définit l'album favori auquel appartient la piste.
     *
     * @param FavoriteAlbum|null $album L'album favori.
     *
     * @return self
     */
    public function setAlbum(?FavoriteAlbum $album): self
    {
        $this->album = $album;

        return $this;
    }
}
