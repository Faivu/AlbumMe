<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    private const INITIAL_LIMIT = 20;
    private const LOAD_MORE_LIMIT = 10;

    #[Route('/', name: 'app_homepage')]
    public function index(Request $request, AlbumRepository $albumRepository): Response
    {
        $offset = $request->query->getInt('offset', 0);
        $isPartial = $request->query->getBoolean('partial', false);

        $limit = $offset === 0 ? self::INITIAL_LIMIT : self::LOAD_MORE_LIMIT;
        $albums = $albumRepository->findPaginated($offset, $limit);
        $hasMore = ($offset + $limit) < $albumRepository->countAll();
        $nextOffset = $offset + $limit;

        if ($isPartial) {
            $response = $this->render('partials/_album_cards.html.twig', [
                'albums' => $albums,
            ]);
            $response->headers->set('X-Has-More', $hasMore ? '1' : '0');
            $response->headers->set('X-Next-Offset', $nextOffset);
            return $response;
        }

        return $this->render('homepage.html.twig', [
            'albums' => $albums,
            'hasMore' => $hasMore,
            'nextOffset' => $nextOffset,
        ]);
    }
}
