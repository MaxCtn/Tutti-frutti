<?php

namespace App\Repository;

use App\Entity\FavoriteAlbum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavoriteAlbum>
 */
class FavoriteAlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteAlbum::class);
    }

    /**
     * Find favorite albums by criteria with optional sorting.
     */
    public function findByCriteria(array $filters = [], string $sortBy = 'title', string $order = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('fa')
            ->leftJoin('fa.label', 'label')
            ->leftJoin('fa.genre', 'genre')
            ->leftJoin('fa.format', 'format')
            ->leftJoin('fa.fruits', 'fruit') // Jointure avec les fruits
            ->addSelect('label', 'genre', 'format', 'fruit');

        // Dynamically apply filters
        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                if ($field === 'title') {
                    $qb->andWhere('fa.title LIKE :title')
                        ->setParameter('title', '%' . $value . '%');
                } elseif ($field === 'format') {
                    $qb->andWhere('format.name LIKE :format')
                        ->setParameter('format', '%' . $value . '%');
                } elseif ($field === 'label') {
                    $qb->andWhere('label.name LIKE :label')
                        ->setParameter('label', '%' . $value . '%');
                } elseif ($field === 'genre') {
                    $qb->andWhere('genre.name LIKE :genre')
                        ->setParameter('genre', '%' . $value . '%');
                } elseif ($field === 'fruit') {
                    $qb->andWhere('fruit.name LIKE :fruit') // Filtre partiel pour fruit
                    ->setParameter('fruit', '%' . $value . '%');
                } elseif ($field === 'year') {
                    $qb->andWhere('fa.year = :year')
                        ->setParameter('year', $value);
                }
            }
        }

        // Sorting
        $sortByField = in_array($sortBy, ['title', 'year'], true) ? "fa.$sortBy" : 'fa.title';
        $qb->orderBy($sortByField, $order);

        return $qb->getQuery()->getResult();
    }
}
