<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class loginController extends AbstractController
{
    #[Route('/login', name:'login')]
    public function showLoginPage(): Response
    {
        return $this->render('login.html.twig');
    }
}