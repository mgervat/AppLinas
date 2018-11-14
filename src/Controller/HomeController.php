<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\EditoRepository;
use App\Repository\GalleryRepository;
use App\Repository\SliderRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticleRepository $repo, GalleryRepository $repog, UserRepository $repom, SliderRepository $repos, EditoRepository $repoe)
    {
        $articles = $repo->findBy(['valide' => '1'], ['createdAt' => 'DESC'], 3);
        $galleries = $repog->findBy([], ['id' => 'DESC'], 6);
        $members = $repom->findAll();
        $slider = $repos->findOneBy([]);
        $edito = $repoe->findOneBy([], ['id' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'galleries' => $galleries,
            'members' => $members,
            'slider' => $slider,
            'edito' => $edito
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about() {
        return $this->render('home/about.html.twig', [

        ]);
    }


    /**
     * @Route("/team", name="team")
     */
    public function team(UserRepository $repo) {
        $members = $repo->findAll();

        return $this->render('home/team.html.twig', [
            'members' => $members
        ]);
    }

    /**
     * @Route("/donate", name="donate")
     */
    public function donate() {
        return $this->render('home/donate.html.twig', [

        ]);
    }

    /**
     * @Route("/actions", name="actions")
     */
    public function actions() {
        return $this->render('home/actions.html.twig', [

        ]);
    }

    /**
     * @Route("/gallery", name="gallery")
     */
    public function gallery(GalleryRepository $repo) {

        $galleries = $repo->findAll();
        return $this->render('home/gallery.html.twig', [
            'galleries' => $galleries
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact() {
        return $this->render('home/contact.html.twig', [

        ]);
    }

    /**
     * @Route("/galleries", name="sidebar_galleries")
     */
    public function sidebarGalleries(GalleryRepository $repo) {

        $galleries = $repo->findBy([], ['id' => 'DESC'], 8);

        return $this->render('partials/_galleries.html.twig', [
            'galleries' => $galleries
        ]);
    }

    /**
     * @Route("ajax/registration")
     */
    public function ajaxRegistration (Request $request, UserRepository $repository) {

        $mot = $request->request->get('mot');
        $user = $repository->findBy(['username' => $mot]);

        return new Response(count($user));
    }
}
