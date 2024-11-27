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

    private array $fruitKeywords = [
        'banane', 'pomme', 'fraise', 'orange', 'raisin', 'citron', 'cerise', 'mangue',
        'kiwi', 'poire', 'pastèque', 'ananas', 'prune', 'abricot', 'figue', 'grenade',
        'melon', 'framboise', 'cassis', 'groseille', 'myrtille', 'papaye', 'litchi', 'coco',
        'nectarine', 'pêche', 'goyave', 'carambole', 'kaki', 'longane', 'mûre', 'physalis',
        'fruit de la passion', 'pomelo', 'tamarin', 'quetsche', 'ramboutan', 'canneberge',
        'prunelle', 'sureau', 'mandarine', 'clémentine', 'kumquat', 'citron vert', 'pamplemousse'
    ];


    public function __construct(HttpClientInterface $client, string $consumerKey, string $consumerSecret, string $imageDirectory)
    {
        $this->client = $client;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->imageDirectory = rtrim($imageDirectory, '/'); // Supprime un éventuel "/" final pour éviter des doublons dans les chemins.
    }

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

            foreach ($data['results'] as &$result) {
                $result['fruits'] = $this->findFruitsInText($result['title'] ?? '');
            }

            return $data['results'];
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Erreur lors de la communication avec l\'API Discogs : ' . $e->getMessage());
        }
    }

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

            $data['fruits'] = $this->findFruitsInText($data['title'] ?? '');

            $data['local_cover_image'] = $this->downloadImage($data['images'][0]['uri'] ?? null, $id);

            return $data;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la récupération des détails de l\'album : ' . $e->getMessage());
        }
    }

    private function downloadImage(?string $url, int $albumId): string
    {
        if (!$url) {
            return '/images/placeholder.jpg'; // Utilisez une image de placeholder locale.
        }

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'DiscogsAPI/1.0',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $imagePath = "{$this->imageDirectory}/{$albumId}.jpg";

                // S'assure que le répertoire existe
                if (!is_dir($this->imageDirectory)) {
                    mkdir($this->imageDirectory, 0755, true);
                }

                file_put_contents($imagePath, $response->getContent());

                return "/images/{$albumId}.jpg";
            }
        } catch (\Exception $e) {
            return '/images/placeholder.jpg'; // Retourne une image par défaut en cas d'échec.
        }

        return '/images/placeholder.jpg';
    }

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
