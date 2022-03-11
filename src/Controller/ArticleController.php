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
    #[Route('/article/{slug}', name: 'app_article')]
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
    #[Route('/article/create/{id}', name: 'app_article_create')]
    public function add(Category $category, Request $request, EntityManagerInterface $entityManager): Response {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $file = $form['img']->getData();

            if($file !== null) {
                $ext = $file->guessExtension();
                if(!$ext) {
                    $ext = "bin";
                }
                $img = uniqid() . "." . $ext;
                $file->move($_SERVER['DOCUMENT_ROOT'] . "images/", $img);
            }
            else {
                $img = "placeholder.png";
            }

            $article->setCategory($category)
                ->setDatetime(new \DateTime())
                ->setAuthor($this->getUser())
                ->setSlug($this->post_slug($article->getTitle()))
                ->setImg($img);
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_category', ["id" => $category->getId()]);
        }

        return $this->render('article/add.html.twig', [
            'form' => $form->createView()
            ]
        );
    }

    #[Route('/article/edit/{slug}', name: 'app_article_edit')]
    public function update(Article $article, Request $request, EntityManagerInterface $entityManager): Response {
        $form = $this->createForm(ArticleType::class, $article, []);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $file = $form['img']->getData();

            if($file !== null) {
                $ext = $file->guessExtension();
                if(!$ext) {
                    $ext = "bin";
                }

                if($article->getImg() !== "placeholder.png") {
                    unlink("images/article/" . $article->getImg());
                }

                $img = uniqid() . "." . $ext;
                $file->move($_SERVER['DOCUMENT_ROOT'] . "images/article/", $img);
                $article->setImg($img);
            }

            $article
                ->setSlug($this->post_slug($article->getTitle()))
            ;
            $entityManager->flush();

            if($article->getVisibility() == 1) {
                return $this->redirectToRoute('app_article', ["slug" => $article->getSlug()]);
            }
            else {
                return $this->redirectToRoute('app_category', ["id" => $article->getCategory()->getId()]);
            }

        }

        return $this->render('article/edit.html.twig', [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/article/delete/{id}', name: 'app_article_delete')]
    public function delete(Article $article, EntityManagerInterface $entityManager): Response {
        $entityManager->remove($article);
        if($article->getImg() !== "placeholder.png") {
            unlink("images/article/" . $article->getImg());
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    function remove_accent($str): array|string
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $str);
    }

    function post_slug($str): string
    {
        return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'),
            array('', '-', ''), $this->remove_accent($str)));
    }
}
