<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\FavoriteAlbumRepository;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function userProfile(Request $request, FavoriteAlbumRepository $favoriteAlbumRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupération des critères depuis la requête
        $criteria = [
            'fruit' => $request->query->get('fruit'),
            'year' => $request->query->get('year'),
            'title' => $request->query->get('title'),
            'label' => $request->query->get('label'),
            'genre' => $request->query->get('genre'),
            'format' => $request->query->get('format'),
        ];

        // Paramètres de tri
        $sortBy = $request->query->get('sortBy', 'title'); // Par défaut, tri par titre
        $order = $request->query->get('order', 'ASC'); // Par défaut, ordre croissant

        // Récupération des albums favoris spécifiques à l'utilisateur
        $favorites = $favoriteAlbumRepository->findByUser(
            $user, // On filtre par utilisateur connecté
            array_filter($criteria), // Filtrer les critères non vides
            $sortBy,
            strtoupper($order) === 'ASC' ? 'ASC' : 'DESC' // Validation du tri (ASC ou DESC)
        );

        // Gestion des cas où les images pourraient ne pas exister
        foreach ($favorites as $favorite) {
            if (!$favorite->getCoverImage() || $favorite->getCoverImage() === '') {
                $favorite->setCoverImage('https://via.placeholder.com/150');
            }
        }


        return $this->render('profile/index.html.twig', [
            'favorites' => $favorites,
        ]);
    }
    

}
