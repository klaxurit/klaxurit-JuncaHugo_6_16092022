<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use App\Repository\UserMessageRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AjaxController extends AbstractController
{
    
    #[Route('/ajax/trick', name: 'app_ajax_trick')]
    /**
     * Create jsonObject from requested data
     *
     * @param Request $request
     * @param TrickRepository $trickRepository
     * @param UserInterface|null $user
     * @return Response
     */
    public function ajaxAction(
        Request $request,
        TrickRepository $trickRepository,
        UserInterface $user = null
    ): JsonResponse {
        return $this->json([
            'total' => $trickRepository->getTotalTricks(),
            'tricks' => $trickRepository->getTricks((int)$request->query->get("page")),
            'current_user' => $user ? $user->getId() : "",
        ], 200, [], ['groups' => 'trick:read']);
    }

    #[Route('/ajax/comment', name: 'app_ajax_comment')]
    /**
     * Create jsonObject from requested data
     *
     * @param Request $request
     * @param UserMessageRepository $userMessage
     * @return Response
     */
    public function ajaxCommentAction(
        Request $request,
        UserMessageRepository $userMessage,
    ): Response {
        $page = (int)$request->query->get("page");
        $trickId = (int)$request->query->get("trickid");
        $comments = $userMessage->getCommentsOfATrick($page, $trickId);
        $total = $userMessage->getTotalComments();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonObject = $serializer->serialize([
            'comments' => $comments,
            'total' => $total
        ], 'json', [
            'circular_reference_handler' => function (object $object) {
                return $object->getId();
            },
        ]);

        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }
}
