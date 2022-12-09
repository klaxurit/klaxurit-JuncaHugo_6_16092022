<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTService;
use App\Form\ResetPasswordType;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Form\ResetPasswordRequestType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResendEmailVerifRequestType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    private array $header;
    private object $jwt;

    public function __construct()
    {
        $this->header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        $this->jwt = new JWTService();
    }


    #[Route(path: '/login', name: 'app_login')]
    /**
     * Login user
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    /**
     * Logout user
     *
     * @return void
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forget-pass', name: 'app_forgotten_password')]
    /**
     * Generate token for password reset email
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TokenGeneratorInterface $tokenGeneratorInterface
     * @param EntityManagerInterface $entityManagerInterface
     * @param SendMailService $mail
     * @return Response
     */
    public function forgottenPaswword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGeneratorInterface,
        EntityManagerInterface $entityManagerInterface,
        SendMailService $mail
    ): Response {
        $form = $this->createForm(ResetPasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // get the user by email
            $user = $userRepository->findOneByEmail($form->get('email')->getData());
            // check if we have an user
            if ($user) {
                // generate reset password token
                $token = $tokenGeneratorInterface->generateToken();
                $user->setResetToken($token);
                try {
                    $entityManagerInterface->persist($user);
                    $entityManagerInterface->flush();
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'A problem has occurred.');
                    return $this->redirectToRoute('app_login');
                }
                $this->sendResetPassMail($token, $user, $mail);
                $this->addFlash('success', 'Email sent!');
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('danger', 'A problem has occurred.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    /**
     * Send reset password mail to user
     *
     * @param string $token
     * @param User $user
     * @param SendMailService $mail
     * @return void
     */
    public function sendResetPassMail(string $token, User $user, SendMailService $mail): Response
    {
        // generate reset password link
        $url = $this->generateUrl('app_reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
        // create mail data
        $context = compact('url', 'user');

        try {
            // send email
            $mail->send(
                'no-reply@snowtrick.net',
                $user->getEmail(),
                'Reset password',
                'password_reset',
                $context
            );
        } catch (\Exception $e) {
            $this->addFlash('danger', 'A problem has occurred during email sending.');
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route(path: '/resend-email-verif', name: 'app_resend_email_verif')]
    public function resendEmailVerif(
        Request $request,
        UserRepository $userRepository,
        SendMailService $mail
    ): Response {
        $form = $this->createForm(ResendEmailVerifRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // get the user by email
            $user = $userRepository->findOneByEmail($form->get('email')->getData());
            if ($user->getIsVerified()) {
                $this->addFlash('danger', 'This account is already activated.');
                return $this->redirectToRoute('app_home');
            }
            if ($user) {
                $this->reSendVerifMail($user, $mail);
                $this->addFlash('success', 'Email sent!');
                $this->clearSession();
                return $this->redirectToRoute('app_home');
            }
            // $user = null
            $this->addFlash('danger', 'A problem has occurred.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/resend_verif_email.html.twig', [
            'requestEmailVerif' => $form->createView()
        ]);
    }

    public function reSendVerifMail(User $user, SendMailService $mail): Response
    {
        // generate user's JWT
        $token = $this->jwt->generate($this->header, ['user_id' => $user->getId()], $this->getParameter('app.jwtsecret'));
        try {
            // send email verification
            $mail->send(
                'no-reply@snowtrick.net',
                $user->getEmail(),
                'Email verification from snowtrick\'s account',
                'register',
                compact('user', 'token')
            );
        } catch (\Exception $e) {
            $this->addFlash('danger', 'A problem has occurred during email sending.');
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/forget-pass/{token}', name: 'app_reset_pass')]
    /**
     * Render reset password form
     *
     * @param string $token
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManagerInterface
     * @param UserPasswordHasherInterface $userPasswordHasherInterface
     * @return Response
     */
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface,
        UserPasswordHasherInterface $userPasswordHasherInterface
    ): Response {
        // check if we have token in db
        $user = $userRepository->findOneByResetToken($token);
        if ($user) {
            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // delete token
                $user->setResetToken('');
                $user->setPassword(
                    $userPasswordHasherInterface->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManagerInterface->persist($user);
                $entityManagerInterface->flush();

                $this->addFlash('success', 'The password has been successfully reset');
                return $this->redirectToRoute('app_login');
            }
            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }
        $this->addFlash('danger', "Invalid token.");
        return $this->redirectToRoute('app_login');
    }

    public function clearSession(): void
    {
        $session = new Session();
        $session->clear();
    }
}
