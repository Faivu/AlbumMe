<?php

namespace App\Controller\Api;

use App\Repository\AlbumRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use FOS\RestBundle\Controller\Annotations as REST;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

final class ReviewController extends AbstractController
{
    // Class constants for lazy loading, defaut values
    private const OFFSET = 0;
    private const LOAD_MORE_LIMIT = 20;

    #[REST\Get('api/v1/albums/{album_id}/reviews', name: 'reviews_all')]
    public function getAllReviews($album_id, Request $request, AlbumRepository $albumRepo, ReviewRepository $reviewRepo, SerializerInterface $serializer): Response
    {
        // Default to default values if there are no preferences in the request
        $offset = $request->query->getInt('offset', self::OFFSET);
        $limit  = $request->query->getInt('limit', self::LOAD_MORE_LIMIT);
        
        $album = $albumRepo->find($album_id);

        if (!$album) {
            return new Response(json_encode(['error' => 'Album not found']), 404, ['Content-Type' => 'application/json']);
        }
        
        $reviews = $reviewRepo->findPaginatedByAlbum($album, $offset, $limit);
        
        // To show the number of remaining albums after every load
        $total = $reviewRepo->countByAlbum($album);
        $end = $offset + count($reviews) - 1;

        // I need to set new serialization groups for reviews
        $context = SerializationContext::create()->setGroups(['review_list']);
        $data = $serializer->serialize($reviews, 'json', $context);

        $response = new Response($data, 200, ['Content-Type' => 'application/json']);
        $response->headers->set('Content-Range', "items {$offset}-{$end}/{$total}");

        return $response;
    }

    #[REST\Get('api/v1/albums/{album_id}/reviews/{review_id}', name: 'review_get')]
    public function getReview($album_id, $review_id, AlbumRepository $albumRepo, ReviewRepository $reviewRepo, SerializerInterface $serializer): Response
    {
        $album = $albumRepo->find($album_id);

        if (!$album) {
            return new Response(json_encode(['error' => 'Album not found']), 404, ['Content-Type' => 'application/json']);
        }

        $review = $reviewRepo->find($review_id);

        if (!$review) {
            return new Response(json_encode(['error' => 'Review not found']), 404, ['Content-Type' => 'application/json']);
        }

        $context = SerializationContext::create()->setGroups(['review_detail']);
        $json = $serializer->serialize($review, 'json', $context);

        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }
}
