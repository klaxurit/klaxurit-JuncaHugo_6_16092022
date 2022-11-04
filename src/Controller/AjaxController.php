<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AjaxController extends AbstractController
{
    #[Route('/ajax/trick', name: 'app_ajax_trick')]
    public function ajaxAction(
        Request $request, 
        TrickRepository $trickRepository,
        bool $noMoreTricks = false
        ): Response
    {
        // get page number
        $page = (int)$request->query->get("page");
        // dd($page);
        
        $tricks = $trickRepository->getTricks($page);
        // dd($tricks);

        $encoders = [new JsonEncoder()]; 
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $maxResults = $trickRepository->getTotalTricks();
        // dd($maxResults);

        $jsonObject = $serializer->serialize($tricks, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }
}
