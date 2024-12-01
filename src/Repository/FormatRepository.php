<?php

// src/Repository/FormatRepository.php

namespace App\Repository;

use App\Entity\Format;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Format.
 *
 * Fournit des méthodes de base pour interagir avec la table des formats et
 * permet d'ajouter des requêtes personnalisées si nécessaire.
 *
 * @extends ServiceEntityRepository<Format>
 */
class FormatRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository.
     *
     * @param ManagerRegistry $registry Le registre des gestionnaires d'entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Format::class);
    }

    // Ajoutez ici vos méthodes personnalisées si nécessaire
}
