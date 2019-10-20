<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentFormType;
use App\Form\PostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {

        return $this->render('home.html.twig');
    }

    /**
     * @Route("/post", name="post_list")
     */
    public function index()
    {
        $form = $this->createForm(PostFormType::class);

        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'newpost' => $form->createView()
        ]);
    }

    /**
     * @Route("post/new", name="post_create")
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $response = new Response();
        $post = new Post();
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();


        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        // gérer les données recues
        // valider les données
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $newpost = $form->getData();

                // enregistrer le commentaire
                $manager = $this->getDoctrine()->getManager();

                $manager->persist($newpost);
                $manager->flush();

                return $this->redirectToRoute('post_list');
            } else {
                $response->setStatusCode(400);
            }
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'newpost' => $form->createView()
        ], $response);
    }

    /**
     * @Route("/posts/{id}", name="post_single", requirements={"id"="[0-9]+"})
     */
    public function single($id) {

        // On va chercher en BDD le post qui correspond à l'ID
        $post = $this->findOr404($id);

        // On crée un formulaire pour les commentaires
        $form = $this->createForm(CommentFormType::class);

        // On passe le post trouvé à la vue
        return $this->render('post/single.html.twig', [
            'post' => $post,
            'comment_form' => $form->createView()
        ]);
    }

    private function findOr404($id) {

        $post = $this
            ->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        if (empty($post)) {
            throw $this->createNotFoundException('Post introuvable');
        }

        return $post;
    }
}
