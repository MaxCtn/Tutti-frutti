<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Service pour interagir avec l'API Discogs.
 */
class DiscogsService
{
    private HttpClientInterface $client;
    private string $consumerKey;
    private string $consumerSecret;

    /**
     * Liste des mots-clés de fruits utilisés pour l'analyse des titres.
     */
    private array $fruitKeywords = [
        'banane', 'pomme', 'fraise', 'orange', 'raisin', 'citron', 'cerise', 'mangue',
        'kiwi', 'poire', 'pastèque', 'ananas', 'prune', 'abricot', 'figue', 'grenade',
        'melon', 'framboise', 'cassis', 'groseille', 'myrtille', 'papaye', 'litchi', 'coco',
        'nectarine', 'pêche', 'goyave', 'carambole', 'kaki', 'longane', 'mûre', 'physalis',
        'fruit de la passion', 'pomelo', 'tamarin', 'quetsche', 'ramboutan', 'canneberge',
        'prunelle', 'sureau', 'mandarine', 'clémentine', 'kumquat', 'citron vert', 'pamplemousse'
    ];

    /**
     * Constructeur du service Discogs.
     */
    public function __construct(HttpClientInterface $client, string $consumerKey, string $consumerSecret)
    {
        $this->client = $client;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * Recherche des albums correspondant à un terme.
     */
    public function searchAlbums(string $searchTerm): array
    {
        try {
            $response = $this->makeRequest('https://api.discogs.com/database/search', [
                'q' => $searchTerm,
                'type' => 'release',
            ]);

            $albums = $response['results'] ?? [];

            return array_map([$this, 'processAlbumData'], $albums);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Erreur lors de la communication avec l\'API Discogs : ' . $e->getMessage());
        }
    }

    /**
     * Récupère les détails d'un album par son ID.
     */
    public function getAlbumDetails(int $id): array
    {
        try {
            $response = $this->makeRequest("https://api.discogs.com/releases/{$id}");

            if (!isset($response['id'])) {
                throw new \RuntimeException('Détails de l\'album introuvables pour l\'ID fourni.');
            }

            return $this->processAlbumData($response);
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la récupération des détails de l\'album : ' . $e->getMessage());
        }
    }

    /**
     * Traite les données d'un album pour ajouter des fruits, une image de couverture, un label, un genre et un format.
     */
    private function processAlbumData(array $album): array
    {
        $album['fruits'] = $this->findFruitsInText($album['title'] ?? '');
        $album['coverImage'] = $album['images'][0]['uri'] ?? '/images/placeholder.jpg';
        $album['label'] = $album['labels'][0]['name'] ?? 'Label inconnu';
        $album['genre'] = $album['genres'][0] ?? 'Genre inconnu';
        $album['format'] = $album['formats'][0]['name'] ?? 'Format inconnu';

        return $album;
    }

    /**
     * Recherche les mots-clés de fruits dans un texte donné.
     */
    private function findFruitsInText(string $text): array
    {
        $foundFruits = [];

        foreach ($this->fruitKeywords as $fruit) {
            if (stripos($text, $fruit) !== false) {
                $foundFruits[] = ucfirst($fruit);
            }
        }

        return array_unique($foundFruits);
    }

    /**
     * Effectue une requête HTTP vers l'API Discogs.
     */
    private function makeRequest(string $url, array $query = []): array
    {
        $response = $this->client->request('GET', $url, [
            'query' => $query,
            'headers' => [
                'Authorization' => "Discogs key={$this->consumerKey}, secret={$this->consumerSecret}",
            ],
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Gère et valide la réponse de l'API Discogs.
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
