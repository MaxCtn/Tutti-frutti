<?php

namespace App\Repository;

use App\Entity\FavoriteAlbum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité FavoriteAlbum.
 *
 * Fournit des méthodes pour interagir avec les albums favoris d'un utilisateur.
 *
 * @extends ServiceEntityRepository<FavoriteAlbum>
 */
class FavoriteAlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteAlbum::class);
    }

    /**
     * Récupère les albums favoris spécifiques à un utilisateur avec des options de filtrage et de tri.
     *
     * @param mixed  $user    L'utilisateur pour lequel récupérer les albums favoris.
     * @param array  $filters Les critères de filtrage (par exemple, 'title', 'fruit', 'year').
     * @param string $sortBy  Le champ sur lequel trier les résultats (par défaut : 'title').
     * @param string $order   L'ordre du tri : 'ASC' ou 'DESC' (par défaut : 'ASC').
     *
     * @return FavoriteAlbum[] La liste des albums favoris correspondant aux critères.
     */
    public function findByUser($user, array $filters = [], string $sortBy = 'title', string $order = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('fa')
            ->where('fa.user = :user')
            ->setParameter('user', $user)
            ->leftJoin('fa.fruits', 'fruit') // Jointure avec les fruits
            ->addSelect('fruit')
            ->leftJoin('fa.label', 'label') // Jointure avec le label
            ->addSelect('label')
            ->leftJoin('fa.genre', 'genre') // Jointure avec le genre
            ->addSelect('genre')
            ->leftJoin('fa.format', 'format') // Jointure avec le format
            ->addSelect('format');

        // Application des filtres
        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                switch ($field) {
                    case 'title':
                        $qb->andWhere('fa.title LIKE :title')
                            ->setParameter('title', '%' . $value . '%');
                        break;
                    case 'fruit':
                        $qb->andWhere('fruit.name LIKE :fruit')
                            ->setParameter('fruit', '%' . $value . '%');
                        break;
                    case 'year':
                        $qb->andWhere('fa.year = :year')
                            ->setParameter('year', $value);
                        break;
                    case 'label':
                        $qb->andWhere('label.name LIKE :label')
                            ->setParameter('label', '%' . $value . '%');
                        break;
                    case 'genre':
                        $qb->andWhere('genre.name LIKE :genre')
                            ->setParameter('genre', '%' . $value . '%');
                        break;
                    case 'format':
                        $qb->andWhere('format.name LIKE :format')
                            ->setParameter('format', '%' . $value . '%');
                        break;
                }
            }
        }

        // Application du tri
        $validSortFields = ['fa.title', 'fa.year', 'label.name', 'genre.name', 'format.name'];
        $sortByField = in_array($sortBy, $validSortFields, true) ? $sortBy : 'fa.title';
        $qb->orderBy($sortByField, strtoupper($order) === 'DESC' ? 'DESC' : 'ASC');

        return $qb->getQuery()->getResult();
    }
}
