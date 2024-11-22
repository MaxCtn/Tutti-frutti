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

    // Liste enrichie de mots-clés de fruits
    private array $fruitKeywords = [
        'banane', 'pomme', 'fraise', 'orange', 'raisin', 'citron', 'cerise', 'mangue',
        'kiwi', 'poire', 'pastèque', 'ananas', 'prune', 'abricot', 'figue', 'grenade',
        'melon', 'framboise', 'cassis', 'groseille', 'myrtille', 'papaye', 'litchi', 'coco'
    ];

    public function __construct(HttpClientInterface $client, string $consumerKey, string $consumerSecret)
    {
        $this->client = $client;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * Effectue une recherche d'albums sur Discogs basée sur un fruit.
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

            // Ajoutez les fruits associés à chaque résultat
            foreach ($data['results'] as &$result) {
                $result['fruits'] = $this->findFruitsInText($result['title'] ?? '');
            }

            return $data['results'];
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

            // Détecter les fruits dans le titre de l'album
            $data['fruits'] = $this->findFruitsInText($data['title'] ?? '');

            // Associer les fruits détectés à chaque morceau
            if (!empty($data['tracklist'])) {
                $data['tracklist'] = $this->associateFruitsWithTracks($data['tracklist']);
            }

            return $data;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la récupération des détails de l\'album : ' . $e->getMessage());
        }
    }

    /**
     * Analyse la liste des morceaux pour détecter les fruits associés.
     */
    private function associateFruitsWithTracks(array $tracklist): array
    {
        foreach ($tracklist as &$track) {
            $track['fruits'] = $this->findFruitsInText($track['title'] ?? '');
        }

        return $tracklist;
    }

    /**
     * Trouve les fruits dans un texte donné.
     */
    private function findFruitsInText(string $text): array
    {
        $foundFruits = [];

        foreach ($this->fruitKeywords as $fruit) {
            if (stripos($text, $fruit) !== false) {
                $foundFruits[] = ucfirst($fruit);
            }
        }

        return array_unique($foundFruits); // Supprime les doublons
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
