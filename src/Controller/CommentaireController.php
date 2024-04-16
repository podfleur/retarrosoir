<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Post;
use App\Entity\Compte;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }

    // Je veux une fonction permettant de récupérer tous les commentaires d'un post et de les formatter pour les rendre exploitables en JSON. Elle prend en paramètre l'id du post
    public function getCommentairesByPostId($idPost, EntityManagerInterface $entityManager): JsonResponse
    {
        $post = $entityManager->getRepository(Post::class)->findOneBy(['id' => $idPost]);

        $commentairesDuPost = $entityManager->getRepository(Commentaire::class)->findBy(['post_id' => $post->getId()], ['date' => 'DESC']);

        $formattedCommentaires = [];

        foreach ($commentairesDuPost as $com) {

            $username = $com->getCompteId()->getUsername();

            $formattedCommentaires[] = [
                'id' => $com->getId(),
                'commentaire' => $com->getTexte(),
                'date' => $com->getDate()->format('Y-m-d H:i:s'),
                'post_id' => $com->getPostId()->getId(),
                'compte_id' => $com->getCompteId()->getId(),
                'username' => $username
            ];
        }

        return $formattedCommentaires ? new JsonResponse($formattedCommentaires) : new JsonResponse(['error' => 'Aucun commentaire pour ce post']);
    }

    #[Route('/new', name: 'app_commentaire_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();

        $texte = $request->get('commentaire');
        $idPost = $request-> get('id');

        $post = $entityManager->getRepository(Post::class)->findOneBy(['id' => $idPost]);

        $commentaire->setPostId($post);
        $commentaire->setCompteId($this->getUser());
        $commentaire->setDate(new \DateTimeImmutable());
        $commentaire->setTexte($texte);

        try {
            $entityManager->persist($commentaire);
            $entityManager->flush();

            $formattedCommentaires = $this->getCommentairesByPostId($idPost, $entityManager);

            return new JsonResponse(['code' => 200, 'commentaires' => $formattedCommentaires->getContent()]);

        } catch (Exception $e) {
            return new JsonResponse(['error' => $e]);
        }

        
    }

    // On fait une route permettant d'afficher tous les commentaires d'un post et de les renvoyer en JSON
    #[Route('/commentaire_post/{id}', name: 'app_commentaire_post', methods: ['GET'])]
    public function getCommentairesByPost($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $formattedCommentaires = $this->getCommentairesByPostId($id, $entityManager);

        return new JsonResponse(['code' => 200, 'commentaires' => $formattedCommentaires->getContent()]);
    }


    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {

        $referer = $request->headers->get('referer');

        $compte = $this->getUser();
        $compte = $entityManager->getRepository(Compte::class)->findOneBy(['username' => $compte->getUserIdentifier()]);

        if ($compte == $commentaire->getCompteId() || $compte == $commentaire->getPostId()->getCompteId()) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }
        // On vérifie si le commentaire appartient bien à l'utilisateur connecté ou s'il est admin ou si le commentaire appartient à un post appartenant à l'utilisateur connecté
        else if ($compte != $commentaire->getCompteId() && $compte != $commentaire->getPostId()->getCompteId() || $compte != $commentaire->getPostId()->getCompteId()) {
            return new JsonResponse(
                [
                    'error' => 'Vous n\'avez pas les droits pour supprimer ce commentaire', 
                    'code' => 403,  
                    'commentaire' => $commentaire->getId(), 
                    'compte' => $compte->getId(),
                    'commentaire_compte' => $commentaire->getCompteId()->getId(),
                    'post_compte' => $commentaire->getPostId()->getCompteId()->getId()
                ]
            );
        }
        
        $entityManager->remove($commentaire);
        $entityManager->flush();

        return $this->redirect($referer);
    }

}
