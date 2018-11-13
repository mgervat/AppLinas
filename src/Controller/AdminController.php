<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Edito;
use App\Entity\Gallery;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\EditoType;
use App\Form\MemberProfilType;
use App\Form\MemberRegistrationType;
use App\Form\SliderType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\EditoRepository;
use App\Repository\GalleryRepository;
use App\Repository\RoleRepository;
use App\Repository\SliderRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
            'articles' => $articles
        ]);
    }


    /**
     * @Route("/admin/edito", name="edito", methods="GET|POST")
     */
    public function edito(EditoRepository $repo, Request $request, ObjectManager $manager)
    {
        $edito = $repo->findOneBy([], ['id' => 'DESC']);
        $form = $this->createForm(EditoType::class, $edito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->flush();

            $this->addFlash('success', 'Edito bien modifié');

        }

        return $this->render('admin/edito.html.twig', [
            'edito' => $edito,
            'form' => $form->createView()
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
            'galleries' => $galleries
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
            'article' => $article
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
            'articles' => $articles
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
                    ->setAuthor($user)
                    ->setValide(0);
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', 'Un nouvel article a bien été ajouté');

            return $this->redirectToRoute('articles');
        }

        return $this->render('admin/article_new.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/article/read-{id}", name="article_read")
     */
    public function articleRead(Article $article)
    {

        return $this->render('admin/validate.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/admin/article/edit-{id}", name="article_edit", methods="GET|POST")
     */
    public function articleEdit(Article $article, Request $request, ObjectManager $manager)
    {

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

        $articles = $repository->findBy(['valide' => '1'], ['createdAt' => 'DESC']);
        return $this->render('admin/comments.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/admin/details-{id}", name="details")
     */
    public function commentsDetails($id, CommentRepository $repository, ArticleRepository $repo) {

        $article = $repo->findOneBy(['id' => $id]);
        $comments = $repository->findBy(['article' => $id], ['createdAt' => 'DESC']);
        return $this->render('admin/comments_details.html.twig', [
            'comments' => $comments,
            'article' => $article
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
        $users = $repository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_comments")
     */
    public function userComments(User $userComment) {
        return $this->render('admin/user_comments.html.twig', [
            'userComment' => $userComment
        ]);
    }

    /**
     * @Route("/admin/members", name="members")
     */
    public function members(UserRepository $repository) {
        $members = $repository->findBy([], ['lastname' => 'ASC']);

        return $this->render('admin/members.html.twig', [
            'members' => $members,
        ]);
    }

    /**
     * @Route("/admin/register", name="member_register")
     */
    public function memberRegister(Request $request, RoleRepository $repo, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {
        $roles = $repo->findAll();

        $user = new User();

        $form = $this->createForm(MemberRegistrationType::class, $user);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $lerole = $request->request->get('role');
            $role = $repo->findOneBy(['id' => $lerole]);

            $admin = $repo->findOneBy(['title' => 'ROLE_ADMIN']);

            $password = "password";
            $hash = $encoder->encodePassword($user, $password);

            if ($role->getId() != $admin->getId()) {
                $user->addUserRole($admin);
            }

            $user->addUserRole($role)
                 ->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('members');
        }

        return $this->render('admin/account/member_register.html.twig', [
            'roles' => $roles,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/account/{id}", name="member_profil")
     */
    public function memberProfil(User $user, Request $request, ObjectManager $manager) {
        $connect = $this->getUser();
        if($connect != $user) {
            return $this->redirectToRoute('admin');
        }
        $form = $this->createForm(MemberProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash('success', 'Votre profil a bien été modifié');
        }

        return $this->render('admin/account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/articles/member-{id}", name="member_article")
     */
    public function memberArticle(User $member,  ArticleRepository $repository)
    {

        $articles = $repository->findBy(['author' => $member], ['createdAt' => 'DESC']);
        return $this->render('admin/member_article.html.twig', [
            'articles' => $articles,
            'member' => $member
        ]);
    }

    /**
     * @Route("/admin/slider", name="slider")
     */
    public function slider(Request $request, SliderRepository $repository, ObjectManager $manager) {
        $slider = $repository->findOneBy([]);

        $form = $this->createForm(SliderType::class, $slider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', 'Slider bien modifié');

        }

        return $this->render('admin/slider.html.twig', [
            'slider' => $slider,
            'form' => $form->createView()
        ]);
    }

}
