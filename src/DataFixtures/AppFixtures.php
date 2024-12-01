<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Classe AppFixtures utilisée pour charger des données factices (fixtures) dans la base de données.
 * Elle est exécutée lors de la commande `php bin/console doctrine:fixtures:load`.
 */
class AppFixtures extends Fixture
{
    /**
     * Méthode chargée de créer et de persister les entités de test dans la base de données.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités Doctrine.
     */
    public function load(ObjectManager $manager): void
    {
        // Exemple d'ajout d'un nouveau produit
        // $product = new Product();
        // $product->setName('Produit de test');
        // $manager->persist($product);

        // Valide et enregistre toutes les entités en attente de persistance
        $manager->flush();
    }
}
