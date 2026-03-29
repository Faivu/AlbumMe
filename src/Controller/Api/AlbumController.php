<?php

namespace App\Controller\Api;

use App\Entity\Album;
use App\Form\AlbumAPIType;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as REST;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

final class AlbumController extends AbstractController
{
    // Class constants for lazy loading
    private const OFFSET = 0;
    private const LOAD_MORE_LIMIT = 20;

    #[REST\Get('api/v1/albums', name: 'album_all')]
    public function getAllAlbums(Request $request, AlbumRepository $repo, SerializerInterface $serializer): Response
    {
        // Default to default values if there are no custom ones
        $offset = $request->query->getInt('offset', self::OFFSET);
        $limit  = $request->query->getInt('limit', self::LOAD_MORE_LIMIT);

        $albums = $repo->findPaginated($offset, $limit);

        // To show the number of remaining albums after every load
        $total = $repo->countAll();
        $end = $offset + count($albums) - 1;

        $context = SerializationContext::create()->setGroups(['album_list']);

        //hateos
        $data = $serializer->serialize($albums, 'json', $context);

        $response = new Response($data, 200, ['Content-Type' => 'application/json']);
        // Sending that number in the header since its a RESTFull best practice 
        $response->headers->set('Content-Range', "items {$offset}-{$end}/{$total}");

        return $response;
    }

    #[REST\Get('api/v1/albums/{album_id}', name: 'album_get')]
    public function getAlbum(AlbumRepository $repo, SerializerInterface $serializer, $album_id): Response
    {

        $album = $repo->find($album_id);

        if (!$album) {
            return new Response(json_encode(['error' => 'Album not found']), 404, ['Content-Type' => 'application/json']);
        }

        $context = SerializationContext::create()->setGroups(['album_detail']);
        $json = $serializer->serialize($album, 'json', $context);

        $response = new Response($json, 200, ['Content-Type' => 'application/json']);

        return $response;
    }

    #[REST\Post('api/v1/albums', name: 'album_new')]
    public function createAlbum(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): Response
    {
        // Check if the request is empty
        if (empty($request->getContent())) {
            return new Response(json_encode(['error' => 'Empty request']), 400, ['Content-Type' => 'application/json']);
        }

        // Check the request format
        if (!($request->getContentTypeFormat() === 'json')) {
            return new Response(json_encode(['error' => 'Request is not in JSON']), 400, ['Content-Type' => 'application/json']);
        }

        $data = json_decode($request->getContent(), true);

        $album = new Album();
        $form = $this->createForm(AlbumAPIType::class, $album);
        $form->submit($data);

        // Checking if the content of the request is correct
        if (!$form->isValid()) {
            return new Response(json_encode(['error' => 'Invalid request data']), 400, ['Content-Type' => 'application/json']);
        }

        // need to implement JWT authentication here
        $album->setCreator($this->getUser());

        $em->persist($album);
        $em->flush();

        // This is to return the created album in the response
        $context = SerializationContext::create()->setGroups(['album_detail']);
        $json = $serializer->serialize($album, 'json', $context);

        return new Response($json, 201, ['Content-Type' => 'application/json']);
    }

    #[REST\Put('api/v1/albums/{album_id}', name: 'album_edit')]
    public function editAlbum($album_id, EntityManagerInterface $em, AlbumRepository $repo, SerializerInterface $serializer, Request $request): Response
    {
        if (empty($request->getContent())) {
            return new Response(json_encode(['error' => 'Empty request']), 400, ['Content-Type' => 'application/json']);
        }

        $data = json_decode($request->getContent(), true);

        $album = $repo->find($album_id);
        if (!$album) {
            return new Response(json_encode(['error' => 'Album not found']), 404, ['Content-Type' => 'application/json']);
        }

        $form = $this->createForm(AlbumAPIType::class, $album);
        $form->submit($data);
        if (!$form->isValid()) {
            return new Response(json_encode(['error' => 'Invalid request data']), 400, ['Content-Type' => 'application/json']);
        }

        // I think I need to check if it is the same user here. That's created the album.

        $em->persist($album);
        $em->flush();

        $context = SerializationContext::create()->setGroups(['album_detail']);
        $json = $serializer->serialize($album, 'json', $context);

        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }

    #[REST\Delete('api/v1/albums/{album_id}', name: 'album_delete')]
    public function deleteAlbum($album_id, EntityManagerInterface $em, AlbumRepository $repo, SerializerInterface $serializer): Response
    {
        $album = $repo->find($album_id);

        if (!$album) {
            return new Response(json_encode(['error' => 'Album not found']), 404, ['Content-Type' => 'application/json']);
        }

        $em->remove($album);
        $em->flush();

        return new Response(null, 204, ['Content-Type' => 'application/json']);
    }
}
