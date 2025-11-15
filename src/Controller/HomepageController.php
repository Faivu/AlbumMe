<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    // Include the user ID or similar in the route, like: '/{userID}/main'
    #[Route('/', name:'app_homepage')]
    public function index(): Response
    {
        // Show all the main page, as when the user opens the web application for the first time
        return $this->render('homepage.html.twig');
    }
}