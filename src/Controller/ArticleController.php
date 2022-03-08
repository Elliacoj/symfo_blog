<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @param Article $article
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    #[Route('/article_{id}', name: 'app_article')]
    public function index(Article $article,ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'article' => $article,
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
        }

        return $this->render('article/add.html.twig', [
            'form' => $form->createView()
            ]);
    }
}
