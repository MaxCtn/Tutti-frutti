<?php

namespace App\Controller;

use App\Entity\FavoriteAlbum;
use App\Entity\Fruit;
use App\Entity\Label;
use App\Entity\Genre;
use App\Entity\Format;
use App\Service\DiscogsService;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AlbumSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class AlbumSearchController extends AbstractController
{
    private DiscogsService $discogsService;
    private EntityManagerInterface $entityManager;

    public function __construct(DiscogsService $discogsService, EntityManagerInterface $entityManager)
    {
        $this->discogsService = $discogsService;
        $this->entityManager = $entityManager;
    }

    #[Route('/album/search', name: 'album_search', methods: ['GET', 'POST'])]
    public function search(Request $request): Response
    {
        $form = $this->createForm(AlbumSearchType::class);
        $form->handleRequest($request);
        $results = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $searchTerm = $form->get('searchTerm')->getData();
            try {
                $results = $this->discogsService->searchAlbums($searchTerm);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la recherche. Veuillez réessayer.');
            }
        }

        return $this->render('album/search.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
        ]);
    }

    #[Route('/album/details/{id}', name: 'album_details')]
    public function showDetails(int $id): Response
    {
        try {
            $albumDetails = $this->discogsService->getAlbumDetails($id);

            // Associer les fruits au titre de l'album s'ils existent
            $fruits = $this->findFruitsInText($albumDetails['title'] ?? '');
            $albumDetails['fruits'] = $fruits; // Ajouter les fruits à l'objet de détails
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Détails de l\'album introuvables.');
        }

        return $this->render('album/details.html.twig', [
            'album' => $albumDetails,
        ]);
    }


    #[Route('/album/add-to-favorites/{id}', name: 'add_to_favorites', methods: ['POST'])]
    public function addToFavorites(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        try {
            $albumDetails = $this->discogsService->getAlbumDetails($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la récupération des détails de l\'album'], 500);
        }

        if (empty($albumDetails['id']) || empty($albumDetails['title'])) {
            return new JsonResponse(['error' => 'Données d\'album invalides'], 400);
        }

        $existingAlbum = $this->entityManager->getRepository(FavoriteAlbum::class)
            ->findOneBy(['user' => $user, 'albumId' => $albumDetails['id']]);

        if ($existingAlbum) {
            return new JsonResponse(['error' => 'Cet album est déjà dans vos favoris'], 409);
        }

        $favoriteAlbum = new FavoriteAlbum();
        $favoriteAlbum->setUser($user);
        $favoriteAlbum->setAlbumId($albumDetails['id']);
        $favoriteAlbum->setTitle($albumDetails['title']);
        $favoriteAlbum->setYear($albumDetails['year'] ?? null);
        $favoriteAlbum->setCoverImage($albumDetails['images'][0]['uri'] ?? null);

        $fruits = $this->findFruitsInText($albumDetails['title']);
        foreach ($fruits as $fruitName) {
            $fruit = $this->findOrCreateEntity(Fruit::class, ['name' => $fruitName]);
            $favoriteAlbum->addFruit($fruit);
        }

        $label = $this->findOrCreateEntity(Label::class, ['name' => $albumDetails['labels'][0]['name'] ?? '']);
        $genre = $this->findOrCreateEntity(Genre::class, ['name' => $albumDetails['genres'][0] ?? '']);
        $format = $this->findOrCreateEntity(Format::class, ['name' => $albumDetails['formats'][0]['name'] ?? '']);

        $favoriteAlbum->setLabel($label);
        $favoriteAlbum->setGenre($genre);
        $favoriteAlbum->setFormat($format);

        $this->entityManager->persist($favoriteAlbum);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    private function findFruitsInText(string $text): array
    {
        $fruitKeywords = ['banane', 'pomme', 'fraise', 'orange', 'raisin', 'citron', 'cerise', 'mangue', 'kiwi'];
        $foundFruits = [];

        foreach ($fruitKeywords as $fruit) {
            if (stripos($text, $fruit) !== false) {
                $foundFruits[] = ['name' => ucfirst($fruit)];
            }
        }

        return $foundFruits;
    }


    private function findOrCreateEntity(string $entityClass, array $criteria)
    {
        $entity = $this->entityManager->getRepository($entityClass)->findOneBy($criteria);

        if (!$entity) {
            $entity = new $entityClass();
            foreach ($criteria as $property => $value) {
                $setter = 'set' . ucfirst($property);
                if (method_exists($entity, $setter)) {
                    $entity->$setter($value);
                }
            }
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }

        return $entity;
    }
}
