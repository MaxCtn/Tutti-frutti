<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\FavoriteAlbumRepository;

/**
 * Contrôleur gérant le profil utilisateur et l'affichage des albums favoris.
 */
class ProfileController extends AbstractController
{
    /**
     * Affiche le profil de l'utilisateur et ses albums favoris, avec possibilité de filtrage et de tri.
     *
     * @Route("/profile", name="profile", methods={"GET"})
     *
     * @param Request                 $request                 La requête HTTP.
     * @param FavoriteAlbumRepository $favoriteAlbumRepository Le dépôt des albums favoris.
     *
     * @return Response La réponse HTTP contenant la page du profil utilisateur.
     */
    #[Route('/profile', name: 'profile')]
    public function userProfile(Request $request, FavoriteAlbumRepository $favoriteAlbumRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les albums favoris en fonction des critères de filtrage
        $favorites = $this->getFilteredFavorites(
            $favoriteAlbumRepository,
            $user,
            $request
        );

        return $this->render('profile/index.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    /**
     * Filtre les albums favoris en fonction des critères de l'utilisateur.
     *
     * @param FavoriteAlbumRepository $favoriteAlbumRepository Le dépôt des albums favoris.
     * @param mixed                   $user                    L'utilisateur courant.
     * @param Request                 $request                 La requête HTTP contenant les critères de filtrage.
     *
     * @return array La liste des albums favoris filtrés.
     */
    private function getFilteredFavorites(
        FavoriteAlbumRepository $favoriteAlbumRepository,
                                $user,
        Request $request
    ): array {
        // Critères de filtrage depuis la requête
        $criteria = [
            'fruit'  => $request->query->get('fruit'),
            'year'   => $request->query->get('year'),
            'title'  => $request->query->get('title'),
            'label'  => $request->query->get('label'),
            'genre'  => $request->query->get('genre'),
            'format' => $request->query->get('format'),
        ];

        // Paramètres de tri
        $sortBy = $request->query->get('sortBy', 'title'); // Tri par titre par défaut
        $order = strtoupper($request->query->get('order', 'ASC')) === 'ASC' ? 'ASC' : 'DESC';

        return $favoriteAlbumRepository->findByUser(
            $user,                   // Filtre par utilisateur connecté
            array_filter($criteria), // Supprime les critères vides ou nuls
            $sortBy,                 // Colonne de tri
            $order                   // Ordre de tri
        );
    }
}
