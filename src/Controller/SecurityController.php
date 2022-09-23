<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forget-pass', name:'app_forgotten_password')]
    public function forgottenPaswword(
        Request $request, 
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGeneratorInterface,
        EntityManagerInterface $entityManagerInterface,
        SendMailService $mail
    ): Response
    {
        $form = $this->createForm(ResetPasswordRequestType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // get the user by email
            $user = $userRepository->findOneByEmail($form->get('email')->getData());
            
            // check if we have an user
            if($user) {
                // generate reset password token
                $token = $tokenGeneratorInterface->generateToken();
                $user->setResetToken($token);

                try {
                    $entityManagerInterface->persist($user);
                    $entityManagerInterface->flush();
                } catch(\Exception $e) {
                    $this->addFlash('danger', 'A problem has occurred.');
                    return $this->redirectToRoute('app_login');
                }

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
                } catch(\Exception $e) {
                    $this->addFlash('danger', 'A problem has occurred during email sending.');
                    return $this->redirectToRoute('app_login');
                }

                $this->addFlash('success', 'Email sent!');
                return $this->redirectToRoute('app_login');

            }
            // $user = null
            $this->addFlash('danger', 'A problem has occurred.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/forget-pass/{token}', name:'app_reset_pass')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface,
        UserPasswordHasherInterface $userPasswordHasherInterface
    ): Response
    {
        // check if we have token in db
        $user = $userRepository->findOneByResetToken($token);

        if($user){
            $form = $this->createForm(ResetPasswordType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
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
}
