<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Création des Users
        $users = [];
        $usernames = ['Biff', 'Doc', 'Marty'];

        $avatars = [
            'https://upload.wikimedia.org/wikipedia/en/thumb/1/15/BiffTannenBackToTheFuture1985.jpg/220px-BiffTannenBackToTheFuture1985.jpg',
            'https://sayingimages.com/wp-content/uploads/back-to-the-future-quotes-doc.jpg',
            'https://media.gq.com/photos/55828dcde52bc4b477a9871d/master/w_1600%2Cc_limit/blogs-the-feed-back-to-the-future-2-marty-mcfly-michael-j-fox.jpg'
        ];

        $photos = [
            'https://support.apple.com/content/dam/edam/applecare/images/en_US/music/featured-section-support-for-itunes_2x.png',
            'https://i.udemycdn.com/course/750x422/1514774_14d7_2.jpg',
            'https://www.mongr.fr/data/1000/Images/Conseils/S-equiper/choisir-chaussure-rando/conseil-choisir-chaussures-randonnee.jpg'
        ];

        foreach ($usernames as $k => $name) {

            $user = new User();
            $user->setUsername($name);
            $user->setEmail(strtolower($name) . '@mail.org');
            $user->setBirth($faker->dateTimeThisCentury());
            $user->setAvatar($avatars[$k]);

            //$password = $this->encoder->encodePassword($user,'azeaze');
            //$user->setPassword($password);

            $password = $this->encoder->encodePassword($user,'aze');
            $user->setPassword($password);

            $manager->persist($user);
            array_push($users, $user);

        }


        // Création des Posts
        for ($i = 0; $i < 10; $i++) {

            $content = $faker->realText(250);

            $date = new \DateTime();

            $post = new Post();
            $post->setTitle($faker->jobTitle);
            $post->setContent($content);
            $post->setCreatedAt($date);

            // Photo du post
            $k = array_rand($photos);
            $photo = $photos[$k];
            $post->setPhoto($photo);

            // Creation des commentaires
            $nbComments = rand(3, 8);
            for ($j = 0; $j < $nbComments; $j++) {

                $comment = new Comment();
                $comment->setContent($faker->realText(280));

                $key = array_rand($users);
                $commentAuthor = $users[$key];
                $comment->setAuthor($commentAuthor);

                // Liaison du commentaire à son post
                $comment->setPost($post);
                // OU
                $post->addComment($comment);

                // Persister le commentaire !!
                // (inutile ici, car automatiquement configuré dans l'entité Post (cascade))
                $manager->persist($comment);
            }
            $manager->persist($post);
        }
        $manager->flush();
    }
}
