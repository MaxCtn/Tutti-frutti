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
     * Récupère les albums favoris spécifiques à un utilisateur avec des filtres et tri.
     */
    public function findByUser($user, array $filters = [], string $sortBy = 'title', string $order = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('fa')
            ->where('fa.user = :user')
            ->setParameter('user', $user)
            ->leftJoin('fa.fruits', 'fruit') // Jointure avec les fruits
            ->addSelect('fruit');

        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                if ($field === 'title') {
                    $qb->andWhere('fa.title LIKE :title')
                        ->setParameter('title', '%' . $value . '%');
                } elseif ($field === 'fruit') {
                    $qb->andWhere('fruit.name LIKE :fruit')
                        ->setParameter('fruit', '%' . $value . '%');
                } elseif ($field === 'year') {
                    $qb->andWhere('fa.year = :year')
                        ->setParameter('year', $value);
                }
            }
        }

        $sortByField = in_array($sortBy, ['title', 'year'], true) ? "fa.$sortBy" : 'fa.title';
        $qb->orderBy($sortByField, strtoupper($order) === 'DESC' ? 'DESC' : 'ASC');

        return $qb->getQuery()->getResult();
    }


}
