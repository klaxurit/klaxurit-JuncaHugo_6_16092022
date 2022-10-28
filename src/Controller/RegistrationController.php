<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
        JWTService $jwt
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // create header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            // create payload
            $payload = [
                'user_id' => $user->getId()
            ];
            // generate user's JWT
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // send email verification
            $mail->send(
                'no-reply@snowtrick.net',
                $user->getEmail(),
                'Email verification from snowtrick\'s account',
                'register',
                compact('user', 'token')
            );

            $this->addFlash('success', 'Verification email sent, please check your email box.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // check is token is valid, not expired and not modified
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            // get payload
            $payload = $jwt->getPayload($token);

            // get user's token
            $user = $userRepository->find($payload['user_id']);

            // check if user exist and doesnt have verify his account
            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $entityManager->flush($user);
                $this->addFlash('success', 'User\'s acount activated !');
                return $this->redirectToRoute('app_login');
            }
        }
        // token's problem
        $this->addFlash('danger', 'Invalid token or expired');
        return $this->redirectToRoute('app_login');
    }

    #[Route('resendverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'You have to be logged to access this page.');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsVerified()) {
            $this->addFlash('danger', 'This account is already activated.');
            return $this->redirectToRoute('app_home');
        }

        // create header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        // create payload
        $payload = [
            'user_id' => $user->getId()
        ];
        // generate user's JWT
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // send email verification
        $mail->send(
            'no-reply@snowtrick.net',
            $user->getEmail(),
            'Email verification from snowtrick\'s account',
            'register',
            compact('user', 'token')
        );

        $this->addFlash('success', 'Verification email sent.');
        return $this->redirectToRoute('app_login');
    }
}
