<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use DateTimeImmutable;
use App\Form\TrickType;
use App\Entity\UserMessage;
use App\Form\UserMessageType;
use App\Service\TrickManager;
use App\Service\UploaderHelper;
use App\Form\UpdateCoverImageType;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserMessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/trick')]
class TrickController extends AbstractController
{
    private object $trickManager;
    private object $entityManager;
    private object $slugger;
    public const NUMBER_OF_TRICK_PER_PAGE = 10;

    public function __construct(TrickManager $trickManager, SluggerInterface $slugger, EntityManagerInterface $entityManager)
    {
        $this->trickManager = $trickManager;
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }
    
    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserInterface $user = null,
        UploaderHelper $uploadedFile,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickSlug = $this->slugger->slug($form->get('name')->getData());
            $trick->setSlug($trickSlug);
            $trick->setUser($user);

            if ($form->get('medias')) {
                $this->mediaTrickManager($form, $trick, $uploadedFile, $this->entityManager);
            }
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/new.html.twig', [
            'trickForm' => $form->createView(),
            'controller_name' => 'TrickController'
        ]);
    }

    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(
        Trick $trick,
        Request $request,
        UserMessageRepository $userMessage,
        MediaRepository $mediaRepository,
        UserInterface $user = null
    ): Response {
        // get comments of page
        $comments = $userMessage->getPaginatedComments((int)$request->query->get("page", 1), TrickController::NUMBER_OF_TRICK_PER_PAGE, $trick);

        // comments parts
        $comment = new UserMessage();
        $commentForm = $this->createForm(UserMessageType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->trickManager->commentTrickManager($comment, $user, $trick);
            $this->addFlash('success', 'Your comment has been successfully submitted!');
            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        $mediaImages = $mediaRepository->findAllMediaImageOfATrick($trick->getId());
        if (!$mediaImages && $user) {
            $this->addFlash('warning', 'To have a cover image on this trick, you must first <a href="/trick/' . $trick->getId() . '/edit">upload images</a>.');
        }

        $form = $this->createForm(UpdateCoverImageType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->trickManager->coverImageTrickManager($trick, $form, );
            $this->addFlash('success', 'Cover image successfully updated!');
            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'total' => $userMessage->getTotalComments(),
            'limit' => TrickController::NUMBER_OF_TRICK_PER_PAGE,
            'page'  => (int)$request->query->get("page", 1),
            'comments'  => $comments,
            'commentForm'   => $commentForm->createView(),
            'trickForm' => $form->createView(),
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Trick $trick,
        TrickRepository $trickRepository,
        UploaderHelper $uploadedFile,
        UserInterface $user = null,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickSlug = $this->slugger->slug($form->get('name')->getData());
            $trick->setSlug($trickSlug);

            // if trick modified by author dont add contributor
            if ($trick->getUser()->getId() !== $user->getId()) {
                $trick->addContributor($user);
            }

            if ($form->get('medias')) {
                $this->mediaTrickManager($form, $trick, $uploadedFile, $this->entityManager);
            }
            $trick->setUpdatedAt(new DateTimeImmutable());
            $trickRepository->add($trick, true);

            $this->addFlash('success', "Trick successfully updated.");
            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('trick/edit.html.twig', [
            'trickForm' => $form->createView(),
            'trick' => $trick,
            'controller_name' => 'TrickController',
        ]);
    }

    #[Route('/delete/{id}', name: 'app_trick_delete', methods: ['DELETE', 'GET'])]
    public function delete(Trick $trick, TrickRepository $trickRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        try {
            $this->denyAccessUnlessGranted('trick_delete', $trick);
            $trickRepository->remove($trick, true);
            $this->addFlash('success', "Trick deleted");

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        } catch (AccessDeniedException) {
            $this->addFlash('danger', "You are not allowed to perform this action.");

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/delete/media/{id}', name: 'app_trick_delete_media', methods: ['DELETE'])]
    public function deleteMedia(Media $media, Request $request, MediaRepository $mediaRepository): JsonResponse
    {
        // dd("ICI");
        $this->denyAccessUnlessGranted('ROLE_USER');
        $data = json_decode($request->getContent(), true);

        // check if token is valid
        $mediaID = $media->getId();
        if ($this->isCsrfTokenValid('delete' . $mediaID, $data['_token'])) {
            // get image name or url
            if ($media->getType() === Media::VIDEO) {
                $mediaName = $media->getUrl();
                $mediaRepository->remove($media, true);

                return new JsonResponse(['success' => 1]);
            } elseif ($media->getTrick()->getCoverImage() && $media->getId() === $media->getTrick()->getCoverImage()->getId()) {
                $mediaName = $media->getFileName();
                // delete image
                unlink($this->getParameter('images_directory') . '/' . $mediaName);
                // remove relation coverImage with trick
                $media->getTrick()->setCoverImage(null);
                // delete from db
                $mediaRepository->remove($media, true);

                return new JsonResponse(['success' => 1]);
            } else {
                $mediaName = $media->getFileName();
                // delete image
                unlink($this->getParameter('images_directory') . '/' . $mediaName);
                // delete from db
                $mediaRepository->remove($media, true);

                return new JsonResponse(['success' => 1]);
            }
        } else {
            return new JsonResponse(['error' => 'Token Invalid'], 400);
        }
    }

    public function mediaTrickManager(
        object $form,
        Trick $trick,
        UploaderHelper $uploadedFile,
    ): void {
        foreach ($form->get('medias') as $mediaForm) {
            if ($mediaForm->get('type')->getData() === "Image") {
                $trickImg = $mediaForm->get('image')->getData();

                try {
                    $filePath = $uploadedFile->uploadTrickImage($trickImg);
                    // get the array from the class
                } catch (FileException $e) {
                    $this->addFlash('danger', "Error on uploading file");
                }

                // stock file name in db
                $media = new Media();
                $media->setType("Image");
                $media->setFileName($filePath);
                $media->setAlt($mediaForm->get('alt')->getData());
                $trick->addMedia($media);
            } else {
                $media = new Media();
                $media->setType("Video");
                $media->setUrl($mediaForm->get('url')->getData());
                $trick->addMedia($media);
            }
        }
        $trick->setTrickGroup($form->get('trickGroup')->getData());
        $this->entityManager->persist($trick);
        $this->entityManager->flush();
    }
}
