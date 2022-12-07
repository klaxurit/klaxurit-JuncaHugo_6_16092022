<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\UserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function commentTrickManager(
        UserMessage $comment,
        UserInterface $user = null,
        Trick $trick
    ): void {
        $comment->setTrick($this->trick);
        $comment->setStatus(false);
        $comment->setUser($user);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }
}