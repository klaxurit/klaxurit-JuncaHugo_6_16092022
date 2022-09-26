<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
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
    public function new(Request $request, TrickRepository $trickRepository, SluggerInterface $slugger): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // get images
            $images = $form->get('images')->getData();

            // loop on images
            foreach($images as $image){
                // generate new filename
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    // copy file in uploads folder
                    $image->move($this->getParameter('images_directory'), $newFilename);
                } catch (FileException $e) {
                    dd($e);
                }
                // stock file name in db
                $img = new Image();
                $img->setName($newFilename);
                $trickSlug = $slugger->slug($form->get('name')->getData());
                $trick->setSlug($trickSlug);
                $trick->addImage($img);
            }
            $trickRepository->add($trick, true);

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
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // get images
            $images = $form->get('images')->getData();

            // loop on images
            foreach($images as $image){
                // generate new filename
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    // copy file in uploads folder
                    $image->move($this->getParameter('images_directory'), $newFilename);
                } catch (FileException $e) {
                    dd($e);
                }
                // stock file name in db
                $img = new Image();
                $img->setName($newFilename);
                $trickSlug = $slugger->slug($form->get('name')->getData());
                $trick->setSlug($trickSlug);
                $trick->addImage($img);
            }
            $trickRepository->add($trick, true);

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $form->createView(),
            'images' => $trick->getImages(),
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
