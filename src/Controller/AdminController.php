<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Painting;
use App\Form\CommentType;
use App\Form\PaintingType;
use App\Repository\CommentRepository;
use App\Repository\PaintingRepository;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @param PaintingRepository $paintingRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/admin', name: 'admin_painting')]
    public function admin(PaintingRepository $paintingRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $paintings = $paintingRepository->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        $pagination = $paginator->paginate(
            $paintings,
            $request->query->getInt('page',1),
            5
        );
        return $this->render('pages/admin/admin.html.twig', [
            'paintings' => $pagination,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param PaintingRepository $paintingRepository
     * @return Response
     */
    #[Route('/admin/tableau/new', name: 'admin_painting_new')]
    public function new(Request $request, EntityManagerInterface $manager, PaintingRepository $paintingRepository): Response
    {
        $painting = new Painting();
        $form = $this->createForm(PaintingType::class, $painting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ){
            $painting->createSlug();
            $painting->setCreatedAt(new \DateTimeImmutable());
            $imageFile = $form->get('imageName')->getData();
            $slugify = new Slugify();
            $newImageName = $slugify->slugify($form->get('title')->getData()) . '-' . uniqid() . '.' . $imageFile->guessExtension();
            $painting->setImageName($newImageName);
            try {
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/img/tableau', $newImageName
                );
            } catch (FileException $e) {
                $this->addFlash('danger', $e->getMessage());
                return $this->redirectToRoute('admin_painting_new');
            }

            $manager->persist($painting);
            $manager->flush();

            $this->addFlash('success', 'Le tableau "' . $painting->getTitle() . '" a été crée avec succès');
            return $this->redirectToRoute('admin_painting');
        }

        return $this->renderForm('pages/admin/pictureNew.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Painting $painting
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/tableau/edit/{id}', name: 'admin_painting_edit')]
    public function edit(EntityManagerInterface $manager,Painting $painting,Request $request ): Response
    {
        $current_dir_path = getcwd();
        $currentImg = $painting->getImageName();
        $painting->setImageName(new File($current_dir_path.'/img/tableau/'.$painting->getImageName()));
        $form = $this->createForm(PaintingType::class, $painting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $painting->createSlug();
            if (!empty($form->get('imageName')->getData())) {
                $imageFile = $form->get('imageName')->getData();
                $file = new Filesystem();
                if ($file->exists($current_dir_path.'/img/tableau/'.$currentImg)) {
                    $file->remove($current_dir_path.'/img/tableau/'.$currentImg);
                }
                $slugify = new Slugify();
                $newImageName = $slugify->slugify($form->get('title')->getData()) . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $painting->setImageName($newImageName);
                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/img/tableau', $newImageName
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', $e->getMessage());
                    return $this->redirectToRoute('admin_painting_new');
                }
            } else {
                $painting->setImageName($currentImg);
            }
            $manager->persist($painting);
            $manager->flush();
            $this->addFlash('success','Le tableau "'.$painting->getTitle().'" a bien été mis à jour');
            return $this->redirectToRoute('admin_painting');
        }
        return $this->renderForm('pages/admin/pictureEdit.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @param Painting $painting
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/tableau/delete/{id}', name: 'admin_painting_delete')]
    public function delete(Painting $painting, EntityManagerInterface $manager): Response
    {
        $file = new Filesystem();
        $current_dir_path = getcwd();
        if ($file->exists($current_dir_path.'/img/tableau/'.$painting->getImageName())) {
            $file->remove($current_dir_path.'/img/tableau/'.$painting->getImageName());
        }
        $manager->remove($painting);
        $manager->flush();
        $this->addFlash('success','Le tableau "'.$painting->getTitle().'" a bien été supprimé');
        return $this->redirectToRoute('admin_painting');
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Painting $painting
     * @return Response
     */
    #[Route('/admin/tableau/view/{id}', name: 'admin_painting_view')]
    public function view(EntityManagerInterface $manager, Painting $painting): Response
    {
        $painting->setIsPublished(!$painting->isIsPublished());
        $manager->flush();
        if ($painting->isIsPublished()){
            $sneak = 'affiché';
        } else {
            $sneak = 'caché';
        }
        $this->addFlash('success', 'Le tableau "'.$painting->getTitle().'" a bien été '.$sneak);
        return $this->redirectToRoute('admin_painting');
    }

    /**
     * @param CommentRepository $commentRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/comment', name: 'admin_comment')]
    public function comment(CommentRepository $commentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $comments = $commentRepository->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        $pagination = $paginator->paginate(
            $comments,
            $request->query->getInt('page',1),
            15
        );
        return $this->render('pages/admin/comment.html.twig', [
            'comments' => $pagination,
        ]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Comment $comment
     * @return Response
     */
    #[Route('/admin/comment/view/{id}', name: 'admin_comment_view')]
    public function commentView(EntityManagerInterface $manager, Comment $comment): Response
    {
        $comment->setIsPublished(!$comment->isIsPublished());
        $manager->flush();
        if ($comment->isIsPublished()){
            $sneak = 'affiché';
        } else {
            $sneak = 'caché';
        }
        $this->addFlash('success', 'Le commentaire de "'.$comment->getPseudo().'" a bien été '.$sneak);
        return $this->redirectToRoute('admin_comment');
    }

    /**
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/comment/delete/{id}', name: 'admin_comment_delete')]
    public function commentDelete(Comment $comment, EntityManagerInterface $manager): Response
    {
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash('success','Le commentaire de '.$comment->getPseudo().' a bien été supprimé');
        return $this->redirectToRoute('admin_comment');
    }
}
