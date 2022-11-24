<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Trick;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class TrickVoter extends Voter
{
    const TRICK_VIEW = 'trick_view';
    const TRICK_DELETE = 'trick_delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $trick): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::TRICK_VIEW, self::TRICK_DELETE])
            && $trick instanceof \App\Entity\Trick;
    }

    protected function voteOnAttribute(string $attribute, $trick, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // check if user isAdmin
        if($this->security->isGranted('ROLE_ADMIN')) return true;
        
        if ($attribute === self::TRICK_DELETE) {
            // logic to determine if the user can DELETE
            // return true or false
            return $this->canDelete($trick, $user);
        }
        return false;
    }

    private function canDelete(Trick $trick, User $user){
        // dd($user, $trick->getUser(), $user->getRoles());
        // the trick's owner or admin can delete
        return $user === $trick->getUser() || in_array($user->getRoles(), ['ROLE_ADMIN']);
    }
}
