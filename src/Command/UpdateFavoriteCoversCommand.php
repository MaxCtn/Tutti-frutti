<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-favorite-covers',
    description: 'Met à jour les images par défaut pour les albums favoris sans image.',
)]
class UpdateFavoriteCoversCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $defaultCoverImage = 'https://via.placeholder.com/150';

        // Sélectionner les albums sans image ou avec une image vide
        $query = $this->entityManager->createQuery(
            'SELECT fa.id, fa.albumId, fa.title, fa.coverImage 
             FROM App\Entity\FavoriteAlbum fa 
             WHERE fa.coverImage IS NULL OR TRIM(fa.coverImage) = \'\''
        );

        $albumsWithoutCovers = $query->getResult();

        if (empty($albumsWithoutCovers)) {
            $output->writeln('<info>Aucune ligne à mettre à jour.</info>');
            return Command::SUCCESS;
        }

        // Afficher les albums affectés avant la mise à jour
        $output->writeln('<info>Albums sans image :</info>');
        foreach ($albumsWithoutCovers as $album) {
            $output->writeln(sprintf(
                'ID: %d | Album ID: %d | Titre: %s | Image: %s',
                $album['id'],
                $album['albumId'],
                $album['title'] ?? 'Titre inconnu',
                $album['coverImage'] ?? 'NULL'
            ));
        }

        // Mettre à jour les albums avec une image par défaut
        $updateQuery = $this->entityManager->createQuery(
            'UPDATE App\Entity\FavoriteAlbum fa 
             SET fa.coverImage = :defaultImage 
             WHERE fa.coverImage IS NULL OR TRIM(fa.coverImage) = \'\''
        )->setParameter('defaultImage', $defaultCoverImage);

        $rowsUpdated = $updateQuery->execute();

        $output->writeln(sprintf('<info>Mise à jour terminée. Nombre de lignes mises à jour : %d</info>', $rowsUpdated));

        return Command::SUCCESS;
    }
}
