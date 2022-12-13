<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\UserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TrickManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
    $this->entityManager = $entityManager;
    }

    /**
     * Create comment and associate user
     *
     * @param UserMessage $comment
     * @param UserInterface|null $user
     * @param Trick $trick
     * @return void
     */
    public function commentTrickManager(
        UserMessage $comment,
        UserInterface $user = null,
        Trick $trick
    ): void {
        $comment->setTrick($trick);
        $comment->setStatus(false);
        $comment->setUser($user);
        $rolesArray = $user->getRoles();
        if (in_array("ROLE_ADMIN", $rolesArray)){
            $comment->setStatus(true);
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    /**
     * Add coverimage to a trick
     *
     * @param object $form
     * @param Trick $trick
     * @return void
     */
    public function coverImageTrickManager(
        object $form,
        Trick $trick
    ): void {
        $trickCoverImage = $form->get('cover_image')->getData();
        $trick->setCoverImage($trickCoverImage);
        $this->entityManager->persist($trick);
        $this->entityManager->flush();
    }
}
