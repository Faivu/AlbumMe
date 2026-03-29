<?php

namespace App\Controller\Api;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use FOS\RestBundle\Controller\Annotations as REST;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

final class APIAlbumController extends AbstractController
{
    // Class constants for lazy loading
    private const INITIAL_LIMIT = 0;
    private const LOAD_MORE_LIMIT = 20;

    #[REST\Get('api/v1/albums', name:'album_all')]
    public function getAllAlbums(Request $request, AlbumRepository $repo, SerializerInterface $serializer): Response
    {
        // Default to default values if there are no custom ones
        $offset = $request->query->getInt('offset', self::INITIAL_LIMIT);
        $limit  = $request->query->getInt('limit', self::LOAD_MORE_LIMIT);

        $albums = $repo->findPaginated($offset, $limit);

        // To show the number of remaining albums after every load
        $total = $repo->countAll();
        $end = $offset + count($albums) - 1;

        $context = SerializationContext::create()->setGroups(['album_list']);
        $data = $serializer->serialize($albums, 'json', $context);

        $response = new Response(json_encode($data), 200, ['Content-Type' => 'application/json']);
        // Sending that number in the header since its a RESTFull best practice 
        $response->headers->set('Content-Range', "items {$offset}-{$end}/{$total}");

        return $response;
    }

    #[REST\Get('api/v1/album/{album_id}', name:'album_get')]
    public function getAlbum(AlbumRepository $repo, SerializerInterface $serializer, $album_id): Response
    {

        $album = $repo->find($album_id);

        $context = SerializationContext::create()->setGroups(['album_detail']);
        $json = $serializer->serialize($album, 'json', $context);

        $response = new Response($json, 200, ['Content-Type' => 'application/json']);
        
        return $response;
    }
}
