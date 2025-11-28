<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ReviewController extends AbstractController
{
    #[Route('/review', name: 'app_review')]
    public function index(): Response
    {
        return $this->render('review/index.html.twig', [
            'controller_name' => 'ReviewController',
        ]);
    }


    #[Route(
        '/album/{id}/review/new',
        name: 'new_review',
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_USER')]
    public function new(Album $album, Request $request, EntityManagerInterface $entityManager): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $review->setAlbum($album);
            $review->setReviewer($this->getUser());
            //$review->setComment()

            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('show_album', [
                'id' => $album->getId()
            ]);
        }
        
        // To show foro first time or when submittion fails
        return $this->render('review/new.html.twig', [
            'album' => $album,
            'review_form' => $form
        ]);

    }
}
