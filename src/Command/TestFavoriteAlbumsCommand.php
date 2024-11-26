<?php

namespace App\Command;

use App\Repository\FavoriteAlbumRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:test-favorite-albums')]
class TestFavoriteAlbumsCommand extends Command
{
    private FavoriteAlbumRepository $favoriteAlbumRepository;

    public function __construct(FavoriteAlbumRepository $favoriteAlbumRepository)
    {
        $this->favoriteAlbumRepository = $favoriteAlbumRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Test des albums favoris d\'un utilisateur')
            ->addArgument('userId', InputArgument::REQUIRED, 'ID de l\'utilisateur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = (int) $input->getArgument('userId');

        // Récupère les albums favoris de l'utilisateur
        $favorites = $this->favoriteAlbumRepository->findBy(['user' => $userId]);

        if (empty($favorites)) {
            $output->writeln("Aucun album favori trouvé pour l'utilisateur ID $userId.");
            return Command::SUCCESS;
        }

        foreach ($favorites as $favorite) {
            $output->writeln(sprintf(
                "Album ID: %d | Titre: %s | Année: %s | Image: %s",
                $favorite->getAlbumId(),
                $favorite->getTitle() ?? 'Non renseigné',
                $favorite->getYear() ?? 'Non renseignée',
                $favorite->getCoverImage() ?? 'Aucune image'
            ));
        }

        return Command::SUCCESS;
    }
}
