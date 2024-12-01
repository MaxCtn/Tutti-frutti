<?php

namespace App\Repository;

use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Track.
 *
 * Fournit des méthodes de base pour interagir avec les pistes d'albums (tracks) et
 * permet d'ajouter des méthodes personnalisées pour répondre à des besoins spécifiques.
 *
 * @method Track|null find($id, $lockMode = null, $lockVersion = null)
 * @method Track|null findOneBy(array $criteria, array $orderBy = null)
 * @method Track[]    findAll()
 * @method Track[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository.
     *
     * @param ManagerRegistry $registry Le registre des gestionnaires d'entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Track::class);
    }

    /**
     * Recherche des pistes par titre partiel.
     *
     * @param string $titlePart Le fragment du titre à rechercher.
     * @return Track[] La liste des pistes correspondantes.
     */
    public function findByTitlePart(string $titlePart): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.title LIKE :title')
            ->setParameter('title', '%' . $titlePart . '%')
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des pistes d'un album spécifique.
     *
     * @param int $albumId L'identifiant de l'album.
     * @return Track[] La liste des pistes de l'album.
     */
    public function findByAlbumId(int $albumId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.album = :albumId')
            ->setParameter('albumId', $albumId)
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
