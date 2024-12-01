<?php

namespace App\Controller;

use App\Service\DiscogsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de test pour l'interaction avec l'API Discogs.
 */
class DiscogsTestController extends AbstractController
{
    /**
     * Teste la recherche d'albums en utilisant le service Discogs.
     *
     * @Route("/test-discogs", name="test_discogs", methods={"GET"})
     *
     * @param DiscogsService $discogsService Le service Discogs pour effectuer des requêtes à l'API.
     *
     * @return Response La réponse JSON contenant les résultats de la recherche.
     */
    #[Route('/test-discogs', name: 'test_discogs')]
    public function test(DiscogsService $discogsService): Response
    {
        // Effectue une recherche d'albums avec le mot-clé 'banana'
        $results = $discogsService->searchAlbums('banana');

        // Retourne les résultats au format JSON
        return $this->json($results);
    }
}
