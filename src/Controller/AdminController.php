<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Gallery;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\SliderType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\GalleryRepository;
use App\Repository\MemberRepository;
use App\Repository\SliderRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     *
     */
    public function index(ArticleRepository $repository)
    {
        $user = $this->getUser();

        $articles = $repository->findBy(['valide' => '0'], ['createdAt' => 'DESC']);
        return $this->render('admin/index.html.twig', [
            'articles' => $articles,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/galleries", name="galleries")
     */
    public function galleries(GalleryRepository $repository, Request $request, ObjectManager $manager) {
        $user = $this->getUser();

        if ($request->request->count() > 0) {
            $id = $request->request->get('id');
            $caption = $request->request->get('caption');
            $url = $request->request->get('url');

            if ($id == 0) {
                $gallery = new Gallery();
            } else {
                $gallery = $repository->findOneBy(['id' => $id]);
            }
            $gallery->setCaption($caption)
                    ->setUrl($url);
            $manager->persist($gallery);
            $manager->flush();
        }

        $galleries = $repository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/galleries.html.twig', [
            'galleries' => $galleries,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/gallery/{id}", name="gallery_delete", methods="DELETE")
     */
    public function galleryDelete(Gallery $gallery, ObjectManager $manager, Request $request) {

        if ($this->isCsrfTokenValid('delete'.$gallery->getId(), $request->get('_token'))) {
            $manager->remove($gallery);
            $manager->flush();

            $this->addFlash('success', 'Image bien supprimée');

            return $this->redirectToRoute('galleries');
        }

    }

    /**
     * @Route("/admin/validation-{id}", name="validation")
     */
    public function validation(Article $article)
    {
        $user = $this->getUser();

        return $this->render('admin/validate.html.twig', [
            'article' => $article,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/ajax_validation", name="ajax_validation")
     */
    public function ajaxValidation(Request $request, ObjectManager $manager) {
        $data = "";
        if($request->request->count()>0) {
            $id = $request->request->get('id');
            $repo = $this->getDoctrine()->getRepository(Article::class);
            $article = $repo->findOneBy(['id' => $id]);

            $article->setValide(1);
            $manager->flush();
        }
        return new Response($data);
    }

    /**
     * @Route("/admin/articles", name="articles")
     */
    public function articles(ArticleRepository $repository)
    {
        $user = $this->getUser();

        $articles = $repository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('admin/articles.html.twig', [
            'articles' => $articles,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/article/new", name="article_new")
     */
    public function articleNew(Request $request, ObjectManager $manager) {

        $user = $this->getUser();

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $title = $article->getTitle();
            $article->setSlug($slugify->slugify($title))
                    ->setCreatedAt(new \DateTime())
                    ->setValide(0);
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', 'Un nouvel article a bien été ajouté');

            return $this->redirectToRoute('articles');
        }

        return $this->render('admin/article_new.html.twig', [
            'article' => $article,
            'user' => $user,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/article/read-{id}", name="article_read")
     */
    public function articleRead(Article $article)
    {
        $user = $this->getUser();

        return $this->render('admin/validate.html.twig', [
            'article' => $article,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/article/edit-{id}", name="article_edit", methods="GET|POST")
     */
    public function articleEdit(Article $article, Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $title = $article->getTitle();
            $article->setSlug($slugify->slugify($title));
            $manager->flush();

            $this->addFlash('success', 'Article bien modifié');

            return $this->redirectToRoute('articles');
        }

        return $this->render('admin/article_edit.html.twig', [
            'article' => $article,
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/article/edit-{id}", name="article_delete", methods="DELETE")
     */
    public function articleDelete(Article $article, ObjectManager $manager, Request $request) {

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->get('_token'))) {
            $manager->remove($article);
            $manager->flush();

            $this->addFlash('success', 'Article bien supprimé');

            return $this->redirectToRoute('articles');
        }

    }

    /**
     * @Route("/admin/comments", name="comments")
     */
    public function comments(ArticleRepository $repository) {
        $user = $this->getUser();

        $articles = $repository->findBy(['valide' => '1'], ['createdAt' => 'DESC']);
        return $this->render('admin/comments.html.twig', [
            'articles' => $articles,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/details-{id}", name="details")
     */
    public function commentsDetails($id, CommentRepository $repository, ArticleRepository $repo) {
        $user = $this->getUser();

        $article = $repo->findOneBy(['id' => $id]);
        $comments = $repository->findBy(['article' => $id], ['createdAt' => 'DESC']);
        return $this->render('admin/comments_details.html.twig', [
            'comments' => $comments,
            'article' => $article,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/comment/change", name="ajax_show_comment")
     */
    public function ajaxShowComment(Request $request, ObjectManager $manager) {
        $data = "";
        if($request->request->count()>0) {
            $id = $request->request->get('id');
            $valeur = $request->request->get('valeur');

            $repo = $this->getDoctrine()->getRepository(Comment::class);
            $comment = $repo->findOneBy(['id' => $id]);

            $comment->setShowComment(intval($valeur));
            $manager->flush();
        }
        return new Response($data);
    }

    /**
     * @Route("/admin/users", name="users")
     */
    public function users(UserRepository $repository) {
        $user = $this->getUser();
        $users = $repository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_comments")
     */
    public function userComments(User $userComment) {
        $user = $this->getUser();
        return $this->render('admin/user_comments.html.twig', [
            'user' => $user,
            'userComment' => $userComment
        ]);
    }

    /**
     * @Route("/admin/members", name="members")
     */
    public function members(UserRepository $repository) {
        $user = $this->getUser();
        $members = $repository->findBy([], ['lastname' => 'ASC']);

        return $this->render('admin/members.html.twig', [
            'members' => $members,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/articles/member-{id}", name="member_article")
     */
    public function memberArticle(User $member,  ArticleRepository $repository)
    {
        $user = $this->getUser();


        $articles = $repository->findBy(['author' => $member], ['createdAt' => 'DESC']);
        return $this->render('admin/member_article.html.twig', [
            'articles' => $articles,
            'member' => $member,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/slider", name="slider")
     */
    public function slider(Request $request, SliderRepository $repository, ObjectManager $manager) {
        $user = $this->getUser();
        $slider = $repository->findOneBy([]);

        $form = $this->createForm(SliderType::class, $slider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', 'Slider bien modifié');

        }

        return $this->render('admin/slider.html.twig', [
            'slider' => $slider,
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

}
