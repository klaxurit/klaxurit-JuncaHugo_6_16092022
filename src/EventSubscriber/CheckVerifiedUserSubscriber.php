<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Security\AccountNotVerifiedAuthenticationException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();
        if (!$passport instanceof UserPassportInterface) {
            throw new \Exception('Unexpected passport type');
        }
        $user = $passport->getUser();
        if (!$user instanceof User) {
            throw new \Exception('Unexpected user type');
        }

        if (!$user->getIsVerified()) {
            throw new AccountNotVerifiedAuthenticationException();
        }
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        if (!$event->getException() instanceof AccountNotVerifiedAuthenticationException) {
            return;
        }

        $session = new Session();
        $session->getFlashBag()->add("notVerifiedError", "");

        $response = new RedirectResponse(
            $this->router->generate('app_home')
        );
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginFailureEvent::class => ['onLoginFailure', 0],
            CheckPassportEvent::class => ['onCheckPassport', -10],
        ];
    }
}
