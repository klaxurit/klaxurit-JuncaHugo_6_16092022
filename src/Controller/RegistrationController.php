<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTService;
use App\Service\UploaderHelper;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    private array $header;
    private object $jwt;

    public function __construct()
    {
        $this->header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        $this->jwt = new JWTService;
    }

    #[Route('/register', name: 'app_register')]
    /**
     * Register an user
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @param SendMailService $mail
     * @param UploaderHelper $uploadedFile
     * @return Response
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
        UploaderHelper $uploadedFile
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
            $this->addAvatar($form->get('avatar')->getData(), $user, $uploadedFile);

            $entityManager->persist($user);
            $entityManager->flush();

            try {
                $this->sendVerifMail($user, $mail);
                $this->addFlash('success', 'Verification email sent, please check your email box.');
                return $this->redirectToRoute('app_home');
            } catch(\Exception $e) {
                $this->addFlash('danger', 'A problem has occurred during email sending.');
                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Add avatar to user
     *
     * @param UploadedFile $avatar
     * @param User $user
     * @param UploaderHelper $uploadedFile
     * @return void
     */
    public function addAvatar(UploadedFile $avatar, User $user, UploaderHelper $uploadedFile) {
        try {
            $filePath = $uploadedFile->uploadTrickImage($avatar);
            // get the array form the class
        } catch (FileException $e) {
            $this->addFlash('danger', "Error on uploading file");
        }
        $user->setAvatar($filePath);
    }

    /**
     * Send verification mail to user
     *
     * @param User $user
     * @param SendMailService $mail
     * @return void
     */
    public function sendVerifMail(User $user, SendMailService $mail) {
        // generate user's JWT
        $token = $this->jwt->generate($this->header, ['user_id' => $user->getId()], $this->getParameter('app.jwtsecret'));
        // send email verification
        $mail->send(
            'no-reply@snowtrick.net',
            $user->getEmail(),
            'Email verification from snowtrick\'s account',
            'register',
            compact('user', 'token')
        );
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    /**
     * Verify user's account with token
     *
     * @param string $token
     * @param JWTService $jwt
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function verifyUser(string $token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
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
}
