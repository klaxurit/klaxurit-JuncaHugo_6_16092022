<?php

namespace App\Controller;

use App\Entity\UserMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserMessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * manageComments
     *
     * @return Response
     */
    #[Route('/comments', name: 'comments')]
    public function manageComments(UserMessageRepository $comments): Response
    {
        
        return $this->render('admin/comments.html.twig', [
            'comments' => $comments->findAll(),
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/comments/switch/{id}', name: 'comment_switch_status', methods: ['GET'])]
    public function switchStatus(UserMessageRepository $comments, EntityManagerInterface $entityManager, int $id): Response
    {
        $comment = $comments->findOneById($id);
        if ($comment->isStatus() === false) {
            $comment->setStatus(true);
        } else {
            $comment->setStatus(false);
        }

        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', "Status updated successfully.");
        return $this->redirectToRoute('admin_comments');
    }

    #[Route('/comments/delete/{id}', name: 'comment_delete', methods: ['DELETE', 'GET'])]
    public function deleteComment(UserMessageRepository $userMessageRepository, UserMessage $comment): Response
    {
        try {
            $userMessageRepository->remove($comment, true);
            $this->addFlash('success', "Comment deleted");
            return $this->redirectToRoute('admin_comments', [], Response::HTTP_SEE_OTHER);
        }catch (\Exception $e) {
            $this->addFlash('danger', "You are not allowed to perform this action.");
            return $this->redirectToRoute('admin_comments', [], Response::HTTP_SEE_OTHER);
        }
    }
}
