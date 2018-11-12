<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function blogDetail(Article $article, Request $request, ObjectManager $manager, UserRepository $repo) {

        $repoCategory = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repoCategory->findBy([], ['title'=>'ASC']);
        $rating = 0;

        if($this->getUser()) {
            $user = $repo->findOneBy(['id' => $this->getUser()]);
            foreach ($user->getRating() as $rate) {
                if ($rate->getId() == $article->getId()) $rating = 1;
            }
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                    ->setShowComment(1)
                    ->setArticle($article)
                    ->setUser($this->getUser());
            $manager->persist($comment);
            $manager->flush();
        }


        return $this->render('blog/article.html.twig', [
            'article' => $article,
            'categories' => $categories,
            'rating' => $rating,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/blog/article/ajax", name="ajax_rating")
     */
    public function ajaxRating(Request $request, ObjectManager $manager, ArticleRepository $repo) {

        if($request->request->count() > 0) {
            $id = $request->request->get('id');
            $article = $repo->findOneBy(['id' => $id]);
            if ($this->getUser()) {
                // Change rating
                $user = $this->getUser();
                $user->addRating($article);
                $manager->persist($user);

                //change like
                $like = intval($article->getLiked()) + 1;
                $article->setLiked($like);
                $manager->persist($article);

                $manager->flush();
            }
        }

        return new Response($like);
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
