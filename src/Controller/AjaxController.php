<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Repository\UserMessageRepository;
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
        ): Response
    {
        // get page number
        $page = (int)$request->query->get("page");
        
        $tricks = $trickRepository->getTricks($page);

        $encoders = [new JsonEncoder()]; 
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonObject = $serializer->serialize($tricks, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/ajax/comment', name: 'app_ajax_comment')]
    public function ajaxCommentAction(
        Request $request, 
        UserMessageRepository $userMessage,
        TrickRepository $trickRepository,
        ): Response
    {
        // get page number
        $page = (int)$request->query->get("page");
        $trickId = (int)$request->query->get("trickid");
        $trick = $trickRepository->findOneById($trickId);
        // dd($trick);
        $comments = $userMessage->getComments($page, $trick);
        
        $encoders = [new JsonEncoder()]; 
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        
        $jsonObject = $serializer->serialize($comments, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);
        
        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }
}
