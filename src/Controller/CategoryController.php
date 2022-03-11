<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category_{id}', name: 'app_category')]
    public function index(Category $category, ArticleRepository $articleRepository): Response {
        $articles = $articleRepository->findByCat($category->getId());

        return $this->render('category/index.html.twig', [
            "category" => $category,
            "articles" => $articles
        ]);
    }
}
