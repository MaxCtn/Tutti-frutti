<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DiscogsController extends AbstractController
{
    #[Route('/discogs', name: 'app_discogs')]
    public function index(): Response
    {
        return $this->render('discogs/index.html.twig', [
            'controller_name' => 'DiscogsController',
        ]);
    }
}
