<?php

namespace App\Controller;

use App\Service\DiscogsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscogsTestController extends AbstractController
{
    #[Route('/test-discogs', name: 'test_discogs')]
    public function test(DiscogsService $discogsService): Response
    {
        $results = $discogsService->searchAlbums('banana');
        return $this->json($results);
    }
}
