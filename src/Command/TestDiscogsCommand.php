<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Cette commande permet de tester l'API Discogs en effectuant une recherche simple.
 * Elle affiche les résultats de la recherche pour un mot-clé spécifique.
 */
#[AsCommand(name: 'app:test-discogs')]
class TestDiscogsCommand extends Command
{
    /**
     * Client HTTP pour effectuer des requêtes à l'API Discogs.
     *
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    /**
     * Clé consommateur pour l'API Discogs.
     *
     * @var string
     */
    private string $discogsConsumerKey;

    /**
     * Secret consommateur pour l'API Discogs.
     *
     * @var string
     */
    private string $discogsConsumerSecret;

    /**
     * Constructeur de la commande.
     *
     * @param HttpClientInterface $httpClient Le client HTTP utilisé pour les requêtes.
     *
     * @throws \RuntimeException Si les clés API Discogs ne sont pas définies.
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->discogsConsumerKey = $_ENV['DISCOGS_CONSUMER_KEY'] ?? getenv('DISCOGS_CONSUMER_KEY') ?? '';
        $this->discogsConsumerSecret = $_ENV['DISCOGS_CONSUMER_SECRET'] ?? getenv('DISCOGS_CONSUMER_SECRET') ?? '';

        if (!$this->discogsConsumerKey || !$this->discogsConsumerSecret) {
            throw new \RuntimeException('Les clés API Discogs (DISCOGS_CONSUMER_KEY et DISCOGS_CONSUMER_SECRET) ne sont pas définies.');
        }

        parent::__construct();
    }

    /**
     * Configure la commande en définissant ses arguments et options.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Teste la connexion à l\'API Discogs en effectuant une recherche.')
            ->addArgument('page', InputArgument::OPTIONAL, 'Numéro de la page à afficher', 1);
    }

    /**
     * Exécute la commande.
     *
     * @param InputInterface  $input  L'interface d'entrée.
     * @param OutputInterface $output L'interface de sortie.
     *
     * @return int Le code de statut de la commande.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $page = (int) $input->getArgument('page');
        $perPage = 10; // Nombre de résultats par page

        try {
            // Effectue une requête GET à l'API Discogs pour rechercher le mot-clé 'banana'
            $response = $this->httpClient->request('GET', 'https://api.discogs.com/database/search', [
                'query' => [
                    'q' => 'banana',  // Mot-clé de recherche
                    'key' => $this->discogsConsumerKey,
                    'secret' => $this->discogsConsumerSecret,
                    'page' => $page,
                    'per_page' => $perPage,
                ],
            ]);

            // Convertit la réponse en tableau
            $data = $response->toArray();

            // Affiche les résultats de la recherche
            $output->writeln("Résultats de recherche pour 'banana' - Page $page:");
            foreach ($data['results'] as $result) {
                $output->writeln($result['title']);
            }

            // Affiche les informations de pagination
            $output->writeln("\nPage $page sur " . $data['pagination']['pages']);
            if ($page < $data['pagination']['pages']) {
                $output->writeln('Pour voir la page suivante, lancez :');
                $output->writeln('php bin/console app:test-discogs ' . ($page + 1));
            } else {
                $output->writeln("Fin des résultats.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            // Gère les exceptions et affiche un message d'erreur
            $output->writeln('<error>Erreur lors de la communication avec l\'API Discogs : ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
