<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AjaxController extends AbstractController
{
    #[Route('/ajax/trick', name: 'app_ajax_trick')]
    public function ajaxAction(Request $request, TrickRepository $trickRepository): Response
    {
        // get page number
        $page = (int)$request->query->get("page", 2);
        // dd($page);
        
        $tricks = $trickRepository->getTricks($page);
        // dd($tricks);

        $encoders = [new JsonEncoder()]; 
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonObject = $serializer->serialize($tricks, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
        

        // return $this->json([
        //     'success' => true,
        //     'data' => $tricks
        // ]);

        // if($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
        //     $jsonData = array();
        //     $idx = 0;

        //     foreach($tricks as $trick) {
        //         $temp = array(
        //             'element' => $this->render('trick/_content.html.twig', [
        //                 'tricks' => $tricks,
        //             ])
        //         );
        //         $jsonData[$idx++] = $temp;
        //     }
        //     return new JsonResponse($jsonData);
        // } else {
        //     return $this->render('trick/_ajax.html.twig', [
        //         'controller_name' => 'AjaxController',
        //     ]);
        // }
    }
}
