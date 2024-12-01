<?php

namespace App\Repository;

use App\Entity\Fruit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Fruit.
 *
 * Fournit des méthodes de base pour interagir avec les fruits et
 * permet d'ajouter des méthodes personnalisées pour répondre à des besoins spécifiques.
 *
 * @extends ServiceEntityRepository<Fruit>
 */
class FruitRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository.
     *
     * @param ManagerRegistry $registry Le registre des gestionnaires d'entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fruit::class);
    }

    /**
     * Trouve un fruit par son nom ou le crée s'il n'existe pas.
     *
     * @param string $fruitName Le nom du fruit à rechercher ou à créer.
     * @return Fruit Le fruit trouvé ou nouvellement créé.
     */
    public function findOrCreate(string $fruitName): Fruit
    {
        $fruit = $this->findOneBy(['name' => $fruitName]);

        if (!$fruit) {
            $fruit = new Fruit();
            $fruit->setName($fruitName);
            $this->getEntityManager()->persist($fruit);
            $this->getEntityManager()->flush();
        }

        return $fruit;
    }


}
