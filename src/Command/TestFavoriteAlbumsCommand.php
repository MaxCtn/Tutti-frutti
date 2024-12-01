<?php

namespace App\Command;

use App\Repository\FavoriteAlbumRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cette commande permet de tester et d'afficher les albums favoris d'un utilisateur spécifique.
 */
#[AsCommand(name: 'app:test-favorite-albums')]
class TestFavoriteAlbumsCommand extends Command
{
    /**
     * Référentiel des albums favoris.
     *
     * @var FavoriteAlbumRepository
     */
    private FavoriteAlbumRepository $favoriteAlbumRepository;

    /**
     * Constructeur de la commande.
     *
     * @param FavoriteAlbumRepository $favoriteAlbumRepository Le repository des albums favoris.
     */
    public function __construct(FavoriteAlbumRepository $favoriteAlbumRepository)
    {
        $this->favoriteAlbumRepository = $favoriteAlbumRepository;
        parent::__construct();
    }

    /**
     * Configure la commande en définissant sa description et ses arguments.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Teste les albums favoris d\'un utilisateur')
            ->addArgument('userId', InputArgument::REQUIRED, 'ID de l\'utilisateur');
    }

    /**
     * Exécute la commande pour afficher les albums favoris de l'utilisateur spécifié.
     *
     * @param InputInterface  $input  L'interface d'entrée de la commande.
     * @param OutputInterface $output L'interface de sortie de la commande.
     *
     * @return int Le code de statut de la commande (succès ou échec).
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = (int) $input->getArgument('userId');

        // Récupère les albums favoris de l'utilisateur
        $favorites = $this->favoriteAlbumRepository->findBy(['user' => $userId]);

        if (empty($favorites)) {
            $output->writeln("Aucun album favori trouvé pour l'utilisateur ID $userId.");
            return Command::SUCCESS;
        }

        // Affiche les détails de chaque album favori
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
