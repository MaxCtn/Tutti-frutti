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

        // Récupération des albums favoris avec filtres et tri
        $favorites = $favoriteAlbumRepository->findByCriteria(array_filter($criteria), $sortBy, $order);

        return $this->render('profile/index.html.twig', [
            'favorites' => $favorites,
        ]);
    }
}
