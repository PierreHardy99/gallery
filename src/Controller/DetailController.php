<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Painting;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends AbstractController
{
    /**
     * @param Painting $painting
     * @param CommentRepository $commentRepository
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/detail/{slug}', name: 'detail')]
    public function index(Painting $painting, CommentRepository $commentRepository, Request $request, EntityManagerInterface $manager, PaginatorInterface $paginator): Response
    {
        $comments = $commentRepository->findBy(
            ['painting' => $painting, 'isPublished' => true],
            ['createdAt' => 'DESC']
        );

        $pagination = $paginator->paginate(
            $comments,
            $request->query->getInt('page',1),
            3
        );

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {

            $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setPainting($painting)
                    ->setIsPublished(true);
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'Commentaire ajouté avec succès');
            return $this->redirectToRoute('detail',['slug' => $painting->getSlug()]);
        }

        return $this->render('pages/detail.html.twig', [
            'painting' => $painting,
            'comments' => $pagination,
            'commentForm' => $form->createView()
        ]);
    }
}
