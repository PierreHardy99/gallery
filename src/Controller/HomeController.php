<?php

namespace App\Controller;

use App\Repository\PaintingRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @param PaintingRepository $paintingRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function index(PaintingRepository $paintingRepository, PaginatorInterface $paginator , Request $request): Response
    {

        $paintings = $paintingRepository->findBy(
            ['isPublished' => true],
            ['id' => 'ASC']
        );

        $pagination = $paginator->paginate(
            $paintings,
            $request->query->getInt('page',1),
            9
        );

        return $this->render('pages/home.html.twig', [
            'tabs' => $pagination
        ]);
    }
}
