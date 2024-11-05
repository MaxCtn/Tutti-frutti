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
    private string $discogsApiKey;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->discogsApiKey = $_ENV['DISCOGS_API_KEY'];
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

        $response = $this->httpClient->request('GET', 'https://api.discogs.com/database/search', [
            'query' => [
                'q' => 'banana',  // Exemple de mot-clé pour tester
                'token' => $this->discogsApiKey,
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
    }
}
