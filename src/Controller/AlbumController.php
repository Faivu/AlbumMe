<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AlbumController extends AbstractController
{
    #[Route('/album/{albumName}', name:'album_show')]
    public function mainPage(string $albumName): Response
    {
        return $this->render('album.html.twig', [
            'album_name' => $albumName
        ]);
    }
}