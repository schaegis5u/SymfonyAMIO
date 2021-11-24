<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/game.{_format}", defaults={"_format": "json"})
     */
    public function game(HttpFoundationRequest $request, GameRepository $gameRepository, SerializerInterface $serializer): Response
    {
        $page = $request->get('p', 1);
        $itemCount = 40;
        $search = $request->get('s', '');

        $entities = $gameRepository->findPagination($page, $itemCount, $search);

        return new Response($serializer->serialize($entities, 'json'));
    }
}
