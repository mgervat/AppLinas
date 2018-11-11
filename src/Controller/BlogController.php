<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog/{page<\d+>?1}", name="blog")
     */
    public function index(ArticleRepository $repo, $page)
    {


        $repoCategory = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repoCategory->findBy([], ['title'=>'ASC']);

        $limit = 5;
        $start = $page * $limit - $limit;
        $total = count($repo->findBy(['valide' => '1']));
        $pages = ceil($total / $limit);

        $articles = $repo->findBy(['valide' => '1'], ['createdAt' => 'DESC'], $limit, $start);

        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
            'pages' => $pages,
            'page' => $page,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/blog/{slug}", name="blog_article")
     */
    public function blogDetail(Article $article) {

        $repoCategory = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repoCategory->findBy([], ['title'=>'ASC']);

        return $this->render('blog/article.html.twig', [
            'article' => $article,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/blog/category/{slug}/{page<\d+>?1}", name="blog_category")
     */
    public function blogCategory(Category $category, $page) {

        $repoCategory = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repoCategory->findBy([], ['title'=>'ASC']);

        $repo = $this->getDoctrine()->getRepository(Article::class);

        $limit = 5;
        $start = $page * $limit - $limit;
        $total = count($repo->findBy(['category' => $category, 'valide' => '1']));
        $pages = ceil($total / $limit);

        $articles = $repo->findBy(['category' => $category, 'valide' => '1'], ['createdAt' => 'DESC'], $limit, $start);

        return $this->render('blog/category.html.twig', [
            'category' => $category,
            'categories' => $categories,
            'articles' => $articles,
            'page' => $page,
            'pages' => $pages
        ]);
    }

    /**
     * @Route("blog/articles", name="sidebar_articles")
     */
    public function sidebarArticles(ArticleRepository $repo) {

        $articles = $repo->findBy(['valide' => '1'], ['liked' => 'DESC'], 3);

        return $this->render('partials/_articles.html.twig', [
           'articles' => $articles
        ]);
    }
}
