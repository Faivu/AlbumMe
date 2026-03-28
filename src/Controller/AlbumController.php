<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Album;
use App\Form\AlbumType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class AlbumController extends AbstractController
{
    private const INITIAL_LIMIT = 20;
    private const LOAD_MORE_LIMIT = 10;

    #[Route('/album/new', name: 'new_album', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // To check if its a user that is accessing this:
        if (!$this->getUser()) {
            $this->addFlash(
                'warning',
                'You must be logged in to create an album'
            );
            return $this->redirectToRoute('app_login');
        }

        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            
            $album->setCreator($this->getUser());

            // These two lines are mendatory to save the object to the database, presisting is like getting it read to save then flush saves it, Entity Manager is always needed when updating the database
            $entityManager->persist($album);
            $entityManager->flush();

            return $this->redirectToRoute('show_album', [
                'id' => $album->getId()
                // That is to put the id in the url
            ]);
        }
            return $this->render('/album/new.html.twig', [
            'form' => $form,
            ]);
    }

    #[Route(
        '/album/{id}',
        name: 'show_album',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function show(Album $album, Request $request, ReviewRepository $reviewRepository): Response
    {
        $offset = $request->query->getInt('reviewOffset', 0);
        $isPartial = $request->query->getBoolean('reviewPartial', false);

        $limit = $offset === 0 ? self::INITIAL_LIMIT : self::LOAD_MORE_LIMIT;
        $reviews = $reviewRepository->findPaginatedByAlbum($album, $offset, $limit);
        $hasMore = ($offset + $limit) < $reviewRepository->countByAlbum($album);
        $nextOffset = $offset + $limit;

        if ($isPartial) {
            $response = $this->render('partials/_review_items.html.twig', [
                'reviews' => $reviews,
                'album' => $album,
            ]);
            $response->headers->set('X-Has-More', $hasMore ? '1' : '0');
            $response->headers->set('X-Next-Offset', $nextOffset);
            return $response;
        }

        return $this->render('/album/show.html.twig', [
            'album' => $album,
            'reviews' => $reviews,
            'hasMore' => $hasMore,
            'nextOffset' => $nextOffset,
        ]);
    }
}
