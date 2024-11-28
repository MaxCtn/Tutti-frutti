<?php

namespace App\Controller;

use App\Entity\FavoriteAlbum;
use App\Entity\Fruit;
use App\Form\AlbumSearchType;
use App\Service\DiscogsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
            $results = $this->discogsService->searchAlbums($searchTerm);

            if (empty($results)) {
                $this->addFlash('warning', "Aucun album trouvé pour : \"$searchTerm\".");
            }
        }

        return $this->render('album/search.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
        ]);
    }

    #[Route('/album/details/{id}', name: 'album_details', methods: ['GET'])]
    public function showDetails(int $id, Request $request): Response
    {
        try {
            $albumDetails = $this->discogsService->getAlbumDetails($id);

            if (empty($albumDetails['fruits'])) {
                throw $this->createNotFoundException('Cet album ne contient aucun fruit pertinent.');
            }

            // Récupérer l'origine de la navigation
            $origin = $request->query->get('origin', 'search');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de récupérer les détails de cet album.');
            return $this->redirectToRoute('album_search');
        }

        return $this->render('album/details.html.twig', [
            'album' => $albumDetails,
            'origin' => $origin, // Passer l'origine à la vue
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

            if (empty($albumDetails['fruits'])) {
                return new JsonResponse(['error' => 'Cet album ne contient aucun fruit pertinent.'], 400);
            }

            $existingAlbum = $this->getFavoriteAlbumByUserAndId($user, $albumDetails['id']);

            if ($existingAlbum) {
                return new JsonResponse(['error' => 'Cet album est déjà dans vos favoris'], 409);
            }

            $this->createFavoriteAlbum($user, $albumDetails);

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l\'ajout de l\'album'], 500);
        }
    }

    #[Route('/album/remove-from-favorites/{id}', name: 'remove_from_favorites', methods: ['POST'])]
    public function removeFromFavorites(int $id): Response
    {
        $user = $this->getUser();

        if ($user) {
            $favoriteAlbum = $this->getFavoriteAlbumByUserAndId($user, $id);

            if ($favoriteAlbum) {
                $this->entityManager->remove($favoriteAlbum);
                $this->entityManager->flush();

                $this->addFlash('success', 'L\'album a été retiré de vos favoris.');
            }
        }

        return $this->redirectToRoute('profile');
    }

    /**
     * Récupère un album favori par utilisateur et ID d'album.
     */
    private function getFavoriteAlbumByUserAndId($user, int $albumId): ?FavoriteAlbum
    {
        return $this->entityManager->getRepository(FavoriteAlbum::class)
            ->findOneBy(['user' => $user, 'albumId' => $albumId]);
    }

    /**
     * Crée un album favori pour l'utilisateur.
     */
    private function createFavoriteAlbum($user, array $albumDetails): void
    {
        $favoriteAlbum = new FavoriteAlbum();
        $favoriteAlbum->setUser($user);
        $favoriteAlbum->setAlbumId($albumDetails['id']);
        $favoriteAlbum->setTitle($albumDetails['title']);
        $favoriteAlbum->setYear($albumDetails['year'] ?? null);
        $favoriteAlbum->setCoverImage($albumDetails['coverImage'] ?? '/images/placeholder.jpg');

        foreach ($albumDetails['fruits'] as $fruitName) {
            $fruit = $this->findOrCreateEntity(Fruit::class, ['name' => $fruitName]);
            $favoriteAlbum->addFruit($fruit);
        }

        $this->entityManager->persist($favoriteAlbum);
        $this->entityManager->flush();
    }

    /**
     * Trouve ou crée une entité en fonction des critères donnés.
     */
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
