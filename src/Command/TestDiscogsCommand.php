<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'app:test-discogs')]
class TestDiscogsCommand extends Command
{
    private HttpClientInterface $httpClient;
    private string $discogsConsumerKey;
    private string $discogsConsumerSecret;

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

    protected function configure(): void
    {
        $this
            ->addArgument('page', InputArgument::OPTIONAL, 'Page number', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $page = (int) $input->getArgument('page');
        $perPage = 10; // Nombre de résultats par page

        try {
            $response = $this->httpClient->request('GET', 'https://api.discogs.com/database/search', [
                'query' => [
                    'q' => 'banana',  // Exemple de mot-clé pour tester
                    'key' => $this->discogsConsumerKey,
                    'secret' => $this->discogsConsumerSecret,
                    'page' => $page,
                    'per_page' => $perPage,
                ],
            ]);

            $data = $response->toArray();

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
            $output->writeln('<error>Erreur lors de la communication avec l\'API Discogs : ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
