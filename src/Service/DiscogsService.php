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
    private string $imageDirectory;

    // Liste enrichie de mots-clés de fruits
    private array $fruitKeywords = [
        'banane', 'pomme', 'fraise', 'orange', 'raisin', 'citron', 'cerise', 'mangue',
        'kiwi', 'poire', 'pastèque', 'ananas', 'prune', 'abricot', 'figue', 'grenade',
        'melon', 'framboise', 'cassis', 'groseille', 'myrtille', 'papaye', 'litchi', 'coco'
    ];

    public function __construct(HttpClientInterface $client, string $consumerKey, string $consumerSecret, string $imageDirectory)
    {
        $this->client = $client;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->imageDirectory = $imageDirectory;
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
            $response = $this->client->request('GET', "https://api.discogs.com/releases/{$id}", [
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

            // Téléchargement de l'image de couverture
            if (!empty($data['images'][0]['uri'])) {
                $data['local_cover_image'] = $this->downloadImage($data['images'][0]['uri'], $id);
            } else {
                $data['local_cover_image'] = 'https://via.placeholder.com/150';
            }

            return $data;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la récupération des détails de l\'album : ' . $e->getMessage());
        }
    }

    /**
     * Télécharge et enregistre une image localement.
     */
    private function downloadImage(string $url, int $albumId): string
    {
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'DiscogsAPI/1.0',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $imagePath = "{$this->imageDirectory}/{$albumId}.jpg";
                file_put_contents($imagePath, $response->getContent());

                return "/images/{$albumId}.jpg";
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors du téléchargement de l\'image : ' . $e->getMessage());
        }

        return 'https://via.placeholder.com/150';
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

        return array_unique($foundFruits);
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
