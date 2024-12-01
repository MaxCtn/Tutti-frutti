<?php

namespace App\Repository;

use App\Entity\FavoriteAlbum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité FavoriteAlbum.
 *
 * Fournit des méthodes personnalisées pour interagir avec les albums favoris d'un utilisateur.
 *
 * @extends ServiceEntityRepository<FavoriteAlbum>
 */
class FavoriteAlbumRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository.
     *
     * @param ManagerRegistry $registry Le registre des gestionnaires d'entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteAlbum::class);
    }

    /**
     * Récupère les albums favoris spécifiques à un utilisateur avec des options de filtrage et de tri.
     *
     * @param mixed  $user   L'utilisateur pour lequel récupérer les albums favoris.
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
            ->addSelect('fruit');

        // Application des filtres
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

        // Application du tri
        $sortByField = in_array($sortBy, ['title', 'year'], true) ? "fa.$sortBy" : 'fa.title';
        $qb->orderBy($sortByField, strtoupper($order) === 'DESC' ? 'DESC' : 'ASC');

        // Retourne les résultats
        return $qb->getQuery()->getResult();
    }
}
