<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DiscogsService
{
    private HttpClientInterface $client;
    private string $consumerKey;
    private string $consumerSecret;

    public function __construct(HttpClientInterface $client, string $consumerKey, string $consumerSecret)
    {
        $this->client = $client;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * Effectue une recherche d'albums sur Discogs.
     */
    public function searchAlbums(string $searchTerm): array
    {
        try {
            $response = $this->client->request('GET', 'https://api.discogs.com/database/search', [
                'query' => [
                    'q' => $searchTerm,
                    'type' => 'release',
                ],
                'headers' => [
                    'Authorization' => "Discogs key={$this->consumerKey}, secret={$this->consumerSecret}",
                ],
            ]);

            $data = $this->handleResponse($response);
            return $data['results'] ?? [];
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Erreur lors de la communication avec l\'API Discogs : ' . $e->getMessage());
        }
    }

    /**
     * Récupère les détails d'un album spécifique à partir de son ID.
     */
    public function getAlbumDetails(int $id): array
    {
        try {
            $url = "https://api.discogs.com/releases/{$id}";
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => "Discogs key={$this->consumerKey}, secret={$this->consumerSecret}",
                ],
            ]);

            $data = $this->handleResponse($response);

            if (!isset($data['id'])) {
                throw new \RuntimeException('Détails de l\'album introuvables pour l\'ID fourni.');
            }

            return $data;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la récupération des détails de l\'album : ' . $e->getMessage());
        }
    }


    /**
     * Traite la réponse HTTP et retourne les données.
     */
    private function handleResponse(ResponseInterface $response): array
    {
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('L\'API Discogs a renvoyé une erreur : ' . $response->getStatusCode());
        }

        $data = $response->toArray(false);

        if (empty($data)) {
            throw new \RuntimeException('La réponse de l\'API Discogs est vide ou invalide.');
        }

        return $data;
    }

}
