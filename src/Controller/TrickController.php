<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        UploaderHelper $uploadedFile
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
                    if($mediaForm->get('type')->getData() === "Video") {
                        $trickVideo = $mediaForm->get('url')->getData();
                        $media = new Media();
                        $media->setType($mediaForm->get('type')->getData());
                        $media->setUrl($trickVideo);
                        $trick->addMedia($media);
                    } else {
                        $trickImg = $mediaForm->get('image')->getData();
                        try {
                            $filePath = $uploadedFile->uploadTrickImage($trickImg);
                            // dd($filePath);
                        } catch (FileException $e) {
                            $this->addFlash('danger', "Error on uploading file");
                        }
                        // stock file name in db
                        $media = new Media();
                        $media->setFileName($filePath);
                        $media->setType($mediaForm->get('type')->getData());
                        $media->setAlt($mediaForm->get('alt')->getData());
                        $trick->addMedia($media);
                    }
                    // dd($trickMedia);
                    
                    // $entityManager->persist($trickMedia);
                }
                // dd($media);
                $entityManager->persist($trick);
                $entityManager->flush();
                
            }
            // $trickRepository->add($trick, true);

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $form->createView(),
            'controller_name' => 'TrickController',
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
    public function edit(Request $request, Trick $trick, EntityManagerInterface $entityManager, TrickRepository $trickRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickSlug = $slugger->slug($form->get('name')->getData());
            $trick->setSlug($trickSlug);
            $entityManager->flush();
            // // get images
            // $images = $form->get('images')->getData();

            // // loop on images
            // foreach($images as $image){
            //     // generate new filename
            //     $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            //     $safeFilename = $slugger->slug($originalFilename);
            //     $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

            //     try {
            //         // copy file in uploads folder
            //         $image->move($this->getParameter('images_directory'), $newFilename);
            //     } catch (FileException $e) {
            //         dd($e);
            //     }
            //     // stock file name in db
            //     $img = new Image();
            //     $img->setName($newFilename);
            //     $trickSlug = $slugger->slug($form->get('name')->getData());
            //     $trick->setSlug($trickSlug);
            //     $trick->addImage($img);
            // }
            // $trickRepository->add($trick, true);

            // return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
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
