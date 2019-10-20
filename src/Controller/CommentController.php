<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/posts/{postId}/comment", name="comment_create")
     */
    public function create(Request $request, $postId)
    {

        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(CommentFormType::class);

        $form->handleRequest($request);

        // gérer les données recues
        // valider les données
        if ($form->isSubmitted()) {

            // créer un commentaire
            $comment = $form->getData();
            $comment->setAuthor($this->getUser());

            // associer le commentaire au post
            $post = $this
                ->getDoctrine()
                ->getRepository(Post::class)
                ->find($postId);

            $comment->setPost($post);

            // enregistrer le commentaire
            $manager->persist($comment);
            $manager->flush();
        }

        // rediriger sur la single
        return $this->redirectToRoute('post_single', [
            'id' => $postId
        ]);
    }
}
