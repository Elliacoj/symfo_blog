<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @param Article $article
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/article_{id}', name: 'app_article')]
    public function index(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->addArticle($article)->setAuthor($this->getUser());
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        if($this->getUser()) {
            $user = $this->getUser()->getUserIdentifier();
        }
        else {
            $user = "";
        }

        return $this->render('article/index.html.twig', [
            'article' => $article,
            "form" => $form->createView(),
            "user" => $user
        ]);
    }

    /**
     * @param Category $category
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/article/create_{id}', name: 'app_article_create')]
    public function add(Category $category, Request $request, EntityManagerInterface $entityManager): Response {
        $article = new Article();
        $article->setCategory($category)->setDatetime(new \DateTime())->setAuthor($this->getUser());
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_category', ["id" => $category->getId()]);
        }

        return $this->render('article/add.html.twig', [
            'form' => $form->createView()
            ]
        );
    }

    #[Route('/article/edit_{id}', name: 'app_article_edit')]
    public function update(Article $article, Request $request, EntityManagerInterface $entityManager): Response {
        $form = $this->createForm(ArticleType::class, $article, []);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_article', ["id" => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/article/delete_{id}', name: 'app_article_delete')]
    public function delete(Article $article, EntityManagerInterface $entityManager): Response {
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
