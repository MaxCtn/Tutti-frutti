<?php

// src/Repository/LabelRepository.php

namespace App\Repository;

use App\Entity\Label;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Label.
 *
 * Fournit des méthodes de base pour interagir avec les labels musicaux et
 * permet d'ajouter des méthodes personnalisées pour répondre à des besoins spécifiques.
 *
 * @method Label|null find($id, $lockMode = null, $lockVersion = null)
 * @method Label|null findOneBy(array $criteria, array $orderBy = null)
 * @method Label[]    findAll()
 * @method Label[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LabelRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository.
     *
     * @param ManagerRegistry $registry Le registre des gestionnaires d'entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Label::class);
    }

    /**
     * Recherche des labels par nom partiel.
     *
     * @param string $namePart Le fragment du nom à rechercher.
     * @return Label[] La liste des labels correspondants.
     */
    public function findByNamePart(string $namePart): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.name LIKE :name')
            ->setParameter('name', '%' . $namePart . '%')
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
