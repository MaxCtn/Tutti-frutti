<?php

// src/Repository/GenreRepository.php

namespace App\Repository;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Genre.
 *
 * Fournit des méthodes de base pour interagir avec les genres musicaux et
 * permet d'ajouter des méthodes personnalisées pour répondre à des besoins spécifiques.
 *
 * @extends ServiceEntityRepository<Genre>
 */
class GenreRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository.
     *
     * @param ManagerRegistry $registry Le registre des gestionnaires d'entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    // Ajoutez ici vos méthodes personnalisées si nécessaire

    /**
     * Recherche des genres par nom partiel.
     *
     * @param string $namePart Le fragment du nom à rechercher.
     * @return Genre[] La liste des genres correspondants.
     */
    public function findByNamePart(string $namePart): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.name LIKE :name')
            ->setParameter('name', '%' . $namePart . '%')
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
