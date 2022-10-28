<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxController extends AbstractController
{
    #[Route('/ajax/trick', name: 'app_ajax_trick')]
    public function ajaxAction(Request $request, TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();

        if($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $jsonData = array();
            $idx = 0;

            foreach($tricks as $trick) {
                $temp = array(
                    'name' => $trick->getName()
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        } else {
            return $this->render('trick/_ajax.html.twig', [
                'controller_name' => 'AjaxController',
            ]);
        }
    }
}
