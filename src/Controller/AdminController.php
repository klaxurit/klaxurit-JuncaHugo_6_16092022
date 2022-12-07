<?php

namespace App\Controller;

use App\Entity\UserMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserMessageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    /**
     * Return admin home template
     *
     * @return Response
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }


    #[Route('/comments', name: 'comments')]
    /**
     * Return comments index template with all comments
     *
     * @param UserMessageRepository $comments
     * @return Response
     */
    public function manageComments(UserMessageRepository $comments): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/comments.html.twig', [
            'comments' => $comments->findAllOrderByCreatedAt(),
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/comments/switch/{id}', name: 'comment_switch_status', methods: ['GET'])]
    /**
     * Switch status of a comment to moderate it
     *
     * @param UserMessageRepository $comments
     * @param EntityManagerInterface $entityManager
     * @param integer $id
     * @return Response
     */
    public function switchStatus(UserMessageRepository $comments, EntityManagerInterface $entityManager, int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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
    /**
     * Delete a comment
     *
     * @param UserMessageRepository $userMessageRepository
     * @param UserMessage $comment
     * @return Response
     */
    public function deleteComment(UserMessageRepository $userMessageRepository, UserMessage $comment): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $userMessageRepository->remove($comment, true);
            $this->addFlash('success', "Comment deleted");
            return $this->redirectToRoute('admin_comments', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            $this->addFlash('danger', "You are not allowed to perform this action.");
            return $this->redirectToRoute('admin_comments', [], Response::HTTP_SEE_OTHER);
        }
    }
}
