<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    /**
     * Return template of home page with paginated tricks
     *
     * @param TrickRepository $trickRepository
     * @param Request $request
     * @return Response
     */
    public function index(TrickRepository $trickRepository, Request $request): Response
    {
        // define number of trick on page
        $limit = 6;

        // get page number
        $page = (int)$request->query->get("page", 1);

        // get tricks of page
        $tricks = $trickRepository->getPaginatedTricks($page, $limit);

        //get total number of tricks
        $total = $trickRepository->getTotalTricks();

        return $this->render('home/index.html.twig', compact('tricks', 'total', 'limit', 'page'));
    }
}
