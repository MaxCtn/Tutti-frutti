<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscogsController extends AbstractController
{
    #[Route('/', name: 'search')]
    public function search(Request $request): Response
    {
        $keyword = $request->query->get('keyword');
        $results = [];

        return $this->render('home/index.html.twig', [
            'keyword' => $keyword,
            'results' => $results,
        ]);
    }
}

