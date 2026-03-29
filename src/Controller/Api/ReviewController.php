<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    #[Route('/api/review', name: 'app_api_review')]
    public function index(): Response
    {
        return $this->render('api/review/index.html.twig', [
            'controller_name' => 'Api/ReviewController',
        ]);
    }
}
