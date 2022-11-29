<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Repository\UserMessageRepository;
use App\Security\Voter\TrickVoter;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class AjaxController extends AbstractController
{
    #[Route('/ajax/trick', name: 'app_ajax_trick')]
    public function ajaxAction(
        Request $request,
        TrickRepository $trickRepository,
        UserInterface $user = null
    ): Response {
        // get page number
        $page = (int)$request->query->get("page");

        $tricks = $trickRepository->getTricks($page);

        if ($user) {
            $userId = $user->getId();
        } else {
            $userId = "";
        }

        $total = $trickRepository->getTotalTricks();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonObject = $serializer->serialize([
            'total' => $total,
            'tricks' => $tricks,
            'current_user' => $userId
        ], 'json', [
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
    ): Response {
        $limit = 3;
        // get page number
        $page = (int)$request->query->get("page");
        $trickId = (int)$request->query->get("trickid");
        $trick = $trickRepository->findOneById($trickId);
        $comments = $userMessage->getPaginatedComments($page, $limit, $trick);

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

    public function isOwner(Trick $trick)
    {
        if (!$this->denyAccessUnlessGranted(TrickVoter::TRICK_DELETE, $trick)) {
            return true;
        }
        return false;
    }
}
