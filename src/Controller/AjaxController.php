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
        return $this->json([
            'comments' => $userMessage->getCommentsOfATrick((int)$request->query->get("page"), (int)$request->query->get("trickid")),
            'total' => $userMessage->getTotalComments()
        ], 200, [], ['groups' => 'comment:read']);
    }
}
