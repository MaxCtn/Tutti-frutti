<?php

namespace App\Controller;

use App\Entity\FavoriteAlbum;
use App\Entity\Fruit;
use App\Entity\Label;
use App\Entity\Genre;
use App\Entity\Format;
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
        $page = $request->query->getInt('page', 1);
        $perPage = 9;
        $searchTerm = $form->isSubmitted() && $form->isValid()
            ? $form->get('searchTerm')->getData()
            : $request->query->get('searchTerm', '');

        if (!empty($searchTerm)) {
            $allResults = $this->discogsService->searchAlbums($searchTerm);
            $results = array_slice($allResults, ($page - 1) * $perPage, $perPage);
            $totalPages = ceil(count($allResults) / $perPage);

            if (empty($results)) {
                $this->addFlash('warning', "Aucun album trouvé pour : \"$searchTerm\".");
            }
        } else {
            $totalPages = 1;
        }

        return $this->render('album/search.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
            'page' => $page,
            'total_pages' => $totalPages,
            'searchTerm' => $searchTerm,
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

            $origin = $request->query->get('origin', 'search');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de récupérer les détails de cet album.');
            return $this->redirectToRoute('album_search');
        }

        return $this->render('album/details.html.twig', [
            'album' => $albumDetails,
            'origin' => $origin,
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

            if ($this->getFavoriteAlbumByUserAndId($user, $albumDetails['id'])) {
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

    private function getFavoriteAlbumByUserAndId($user, int $albumId): ?FavoriteAlbum
    {
        return $this->entityManager->getRepository(FavoriteAlbum::class)
            ->findOneBy(['user' => $user, 'albumId' => $albumId]);
    }

    private function createFavoriteAlbum($user, array $albumDetails): void
    {
        $favoriteAlbum = new FavoriteAlbum();
        $favoriteAlbum->setUser($user);
        $favoriteAlbum->setAlbumId($albumDetails['id']);
        $favoriteAlbum->setTitle($albumDetails['title']);
        $favoriteAlbum->setYear($albumDetails['year'] ?? null);
        $favoriteAlbum->setCoverImage($albumDetails['coverImage'] ?? '/images/placeholder.jpg');

        // Persistance des fruits
        foreach ($albumDetails['fruits'] as $fruitName) {
            $fruit = $this->findOrCreateEntity(Fruit::class, ['name' => $fruitName]);
            $favoriteAlbum->addFruit($fruit);
        }

        // Persistance du label
        $label = $this->findOrCreateEntity(Label::class, ['name' => $albumDetails['label']]);
        $favoriteAlbum->setLabel($label);

        // Persistance du genre
        $genre = $this->findOrCreateEntity(Genre::class, ['name' => $albumDetails['genre']]);
        $favoriteAlbum->setGenre($genre);

        // Persistance du format
        $format = $this->findOrCreateEntity(Format::class, ['name' => $albumDetails['format']]);
        $favoriteAlbum->setFormat($format);

        $this->entityManager->persist($favoriteAlbum);
        $this->entityManager->flush();
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
