<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscogsController extends AbstractController
{
    private $httpClient;
    private $discogsToken;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->discogsToken = 'caakKDOKAYTiChTQvmdxEERdAQKKihZxuLvjUbhf';
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request): Response
    {
        $keyword = $request->query->get('keyword');
        $results = [];

        if ($keyword) {
            $response = $this->httpClient->request(
                'GET',
                'https://api.discogs.com/database/search',
                [
                    'query' => [
                        'q' => $keyword,
                        'token' => $this->discogsToken,
                    ],
                ]
            );

            $results = $response->toArray()['results'] ?? [];
        }

        return $this->render('Home/index.html.twig', [
            'keyword' => $keyword,
            'results' => $results,
        ]);
    }

    #[Route('/favorites/add/{id}', name: 'add_to_favorites')]
    public function addToFavorites($id): Response
    {


        $this->addFlash('success', 'L\'élément a été ajouté à vos favoris !');

        return $this->redirectToRoute('search');
    }
}
