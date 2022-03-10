<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    #[Route('/comment/create_{id}', name: 'app_comment_create')]
    public function addComment(Article $article, Request $request, EntityManagerInterface $entityManager): Response {
        $comment = new Comment();
        $comment->addArticle($article)->setAuthor($this->getUser());
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article', ["id" => $article->getId()]);
    }

    #[Route('/comment/delete_{id}', name: 'app_comment_delete')]
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManager): Response {
        $article = $comment->getArticle()->getValues()[0]->getId();
        $entityManager->remove($comment);
        $entityManager->flush();


        return $this->redirectToRoute('app_article', ["id" => $article]);
    }

    #[Route('/comment/edit_{id}', name: 'app_comment_edit')]
    public function editComment(Comment $comment, Request $request, EntityManagerInterface $entityManager): Response {
        $article = $comment->getArticle()->getValues()[0]->getId();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_article', ["id" => $article]);
        }

        return $this->render('comment/index.html.twig', [
                'form' => $form->createView()
            ]
        );
    }
}
