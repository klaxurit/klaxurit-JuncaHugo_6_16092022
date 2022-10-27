<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Service\UploaderHelper;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/trick')]
class TrickController extends AbstractController
{
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
        EntityManagerInterface $entityManager, 
        TrickRepository $trickRepository, 
        SluggerInterface $slugger,
        UploaderHelper $uploadedFile,
        ): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $trickSlug = $slugger->slug($form->get('name')->getData());
            $trick->setSlug($trickSlug);

            if($form->get('medias')) {
                // get media
                foreach ($form->get('medias') as $mediaForm){
                    if($mediaForm->get('type')->getData() === "Image") {
                        $trickImg = $mediaForm->get('image')->getData();
                        
                        try {
                            $filePath = $uploadedFile->uploadTrickImage($trickImg);
                            // get the array form the class
                        } catch (FileException $e) {
                            $this->addFlash('danger', "Error on uploading file");
                        }
                        
                        // stock file name in db
                        $media = new Media();
                        $media->setType("Image");
                        $media->setFileName($filePath);
                        $media->setAlt($mediaForm->get('alt')->getData());
                        $trick->addMedia($media);  
                    }else{
                        $media = new Media();
                        $media->setType("Video");
                        $media->setUrl($mediaForm->get('url')->getData());
                        $trick->addMedia($media);
                    }
                }
                $trick->addGroup($form->get('groups')->getData());
                $entityManager->persist($trick);
                $entityManager->flush();
            }
            // $trickRepository->add($trick, true);
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('trick/new.html.twig', [
            'trick' => $form->createView(),
            'controller_name' => 'TrickController'
        ]);
    }

    #[Route('/{id}', name: 'app_trick_show', methods: ['GET'])]
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(
            Request $request, 
            Trick $trick, 
            EntityManagerInterface $entityManager, 
            TrickRepository $trickRepository, 
            UploaderHelper $uploadedFile,
            SluggerInterface $slugger
        ): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickSlug = $slugger->slug($form->get('name')->getData());
            $trick->setSlug($trickSlug);

            if($form->get('medias')) {
                // get media
                foreach ($form->get('medias') as $mediaForm){
                    if($mediaForm->get('type')->getData() === "Image") {
                        $trickImg = $mediaForm->get('image')->getData();
                        
                        try {
                            $filePath = $uploadedFile->uploadTrickImage($trickImg);
                            // get the array form the class
                        } catch (FileException $e) {
                            $this->addFlash('danger', "Error on uploading file");
                        }

                        // stock file name in db
                        $media = new Media();
                        $media->setType("Image");
                        $media->setFileName($filePath);
                        $media->setAlt($mediaForm->get('alt')->getData());
                        $trick->addMedia($media);  
                    }else{
                        $media = new Media();
                        $media->setType("Video");
                        $media->setUrl($mediaForm->get('url')->getData());
                        $trick->addMedia($media);
                    }
                }
                $trick->addGroup($form->get('groups')->getData());
                $entityManager->persist($trick);
                $entityManager->flush();
            }
            // $trickRepository->add($trick, true);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $form->createView(),
            'controller_name' => 'TrickController',
        ]);
    }

    #[Route('/{id}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
        }

        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete/image/{id}', name: 'app_trick_delete_image', methods: ['DELETE'])]
    public function deleteImage(Image $image, Request $request, ImageRepository $imageRepository){
        $data = json_decode($request->getContent(), true);
        
        // check if token is valid
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            // get image name
            $imageName = $image->getName();
            // delete image
            unlink($this->getParameter('images_directory').'/'.$imageName);
            // delete from db
            $imageRepository->remove($image, true);

            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalid'], 400);
        }
    }
}
