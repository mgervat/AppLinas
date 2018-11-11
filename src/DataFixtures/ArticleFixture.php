<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Gallery;
use App\Entity\Member;
use App\Entity\Role;
use App\Entity\Slider;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ArticleFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $slugify = new Slugify();

        $users = [];
        $genres = ['male', 'female'];

        // création du slider
        $slider = new Slider();
        $slider->setImage1('/images/main-slider/kids-2985782_1920.jpg')
               ->setImage2('/images/main-slider/road-1072823_1920.jpg')
               ->setText1('Le texte 1')
               ->setText2('Le texte 2')
               ->setText3('Le texte 3')
               ->setText4('Le texte 4')
               ->setText5('Le texte 5')
               ->setText6('Le texte 6');
        $manager->persist($slider);

        // création des catégories
        $categories = [];
        $cat= ['Economie', 'Société', 'Culture', 'Evènement', 'Politique'];

        for($i=0; $i<5; $i++) {
            $category = new Category();
            $category->setTitle($cat[$i])
                ->setSlug($slugify->slugify($cat[$i]));
            $manager->persist($category);
            $categories[] = $category;
        }

        // création des Role
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        // création utilisateurs ADMIN
        for($i=0; $i<4; $i++) {
            $user = new User();
            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureImg = $faker->numberBetween(1,99).'.jpg';
            $picture .= ($genre == 'male' ? 'men/' : 'women/').$pictureImg;
            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstname($faker->firstName($genre))
                ->setLastname($faker->lastName)
                ->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setPassword($hash)
                ->setPicture($picture)
                ->addUserRole($adminRole)
                ->setJob('Membre');
            $manager->persist($user);
            $users[] = $user;
        }

        // création des utilisateurs
        for($i=0; $i<10; $i++) {
            $user = new User();
            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureImg = $faker->numberBetween(1,99).'.jpg';
            $picture .= ($genre == 'male' ? 'men/' : 'women/').$pictureImg;
            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstname($faker->firstName($genre))
                ->setLastname($faker->lastName)
                ->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setPassword($hash)
                ->setPicture($picture);
            $manager->persist($user);
            $users[] = $user;
        }

        // création de la galerie
        for($g=0; $g<50; $g++) {
            $gallery = new Gallery();
            $photo = "https://picsum.photos/640/480?image=".$g;
            $gallery->setUrl($photo)
                ->setCaption($faker->words(mt_rand(2,5),true));
            $manager->persist($gallery);
        }

        // créations des membres
        for($m=0; $m<5; $m++) {
            $member = new Member();
            $member->setName($faker->name)
                ->setEmail($faker->email())
                ->setJob($faker->jobTitle())
                ->setImage($faker->imageUrl(195,195,'people'))
                ->setPassword($faker->word);
            $manager->persist($member);

            // création des articles
            for($i=0; $i<mt_rand(3, 10); $i++) {
                $article = new Article;

                $title = $faker->words(mt_rand(3, 6),true);
                $rd = mt_rand(0, 5);
                if ($rd > 3) $video = 'https://www.youtube.com/watch?v=Fvae8nxzVz4'; else $video = null;
                $photo = "https://picsum.photos/640/480?image=".mt_rand(500, 1000);
                $article->setTitle($title)
                    ->setSlug($slugify->slugify($title))
                    ->setShortDescription($faker->sentences(mt_rand(2,6),true))
                    ->setDescription($faker->sentences(mt_rand(10,30),true))
                    ->setQuotation($faker->sentences(mt_rand(5,9),true))
                    ->setImage($photo)
                    ->setVideo($video)
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setMember($member)
                    ->setAuthor($users[mt_rand(0, 4)])
                    ->setCategory($categories[mt_rand(0, 4)])
                    ->setLiked(mt_rand(0, 85))
                    ->setValide(mt_rand(0, 1));

                $manager->persist($article);

                // création des commentaires
                $nombre = mt_rand(0, 5);
                if ($nombre > 0) {

                    for($c=0; $c<=$nombre; $c++) {
                        $comment = new Comment();

                        $now = new \DateTime();
                        $interval = $now->diff($article->getCreatedAt());
                        $days = '-'.$interval->days.' days';

                        $comment->setCreatedAt($faker->dateTimeBetween($days))
                            ->setMessage($faker->sentences(mt_rand(2,6),true))
                            ->setArticle($article)
                            ->setUser($users[mt_rand(0, count($users)-1)])
                            ->setShowComment(1);
                        $manager->persist($comment);
                    }

                }
            }

        }

        $manager->flush();
    }
}