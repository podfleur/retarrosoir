<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Photo;
use App\Entity\Compte;
use App\Entity\Format;
use App\Entity\Like;
use App\Entity\PostPhoto;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $post->setDatePublication(new \DateTime('now'));
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $post->setCompteId($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {

            // On vérifie s'il y a une ou des photos, et si c'est le cas, on les ajoute 
            // dans la table photo et on ajoute de nouvelles données dans la table PostPhoto 
            // comprenant l'id du post et l'id de la photo

            if ($form->get('data')->getData() != null) {
                
                $photos = $form->get('data')->getData();

                // On vérifie s'il y a une ou plusieurs photos
                if (is_array($photos)) {
                    foreach ($photos as $photographie) {
                        $photo = new Photo();
                        $postPhoto = new PostPhoto();
                        $photo->setDonneesPhoto(file_get_contents($photographie->getRealPath()));
                        $postPhoto->setPhotoId($photo);
                        $postPhoto->setPostId($post);
                        $entityManager->persist($postPhoto);

                        // On récupère le ou les type(s) des photos
                        $type = $photographie->getMimeType();

                        // On ajoute un nouveau format pour la photo si il n'existe pas sinon on récupère le format existant et on attribue à la photo l'id du format
                        $format = $entityManager->getRepository(Format::class)->findOneBy(['nom' => $type]);
                        if ($format === null) {
                            $format = new Format();
                            $format->setNom($type);
                            $entityManager->persist($format);
                            $photo->setFormatId($format);
                        } else {
                            $photo->setFormatId($format);
                        }

                        $entityManager->persist($photo);

                    }
                } else {
                    $photo = new Photo();
                    $postPhoto = new PostPhoto();
                    $photo->setDonneesPhoto(file_get_contents($form->get('data')->getData()));
                    $postPhoto->setPhotoId($photo);
                    $postPhoto->setPostId($post);
                    $entityManager->persist($postPhoto);

                    // On récupère le type de la photo
                    $type = $form->get('data')->getData()->getMimeType();

                    // On ajoute un nouveau format pour la photo si il n'existe pas sinon on récupère le format existant et on attribue à la photo l'id du format
                    $format = $entityManager->getRepository(Format::class)->findOneBy(['nom' => $type]);
                    if ($format === null) {
                        $format = new Format();
                        $format->setNom($type);
                        $entityManager->persist($format);
                        $photo->setFormatId($format);
                    } else {
                        $photo->setFormatId($format);
                    }

                    $entityManager->persist($photo);
                }

            }

            $heures = $request->get('heures_retard');
            $minutes = $request->get('minutes_retard');
            
            $tempsRetardEnMinutes = $heures * 60 + $minutes;

            $post->setTempsRetard($tempsRetardEnMinutes);

            $entityManager->persist($post);
            $entityManager->flush();

            // utilisateur connecté
            $user = $this->getUser();
            $compte = $entityManager->getRepository(Compte::class)->findOneBy(['id' => $user]);

            return $this->redirectToRoute('app_compte_show', ['id' => $compte->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On vérifie qu'il n'y ait pas de nouvelles images ou bien qu'elles soient supprimées ou modifiées
            if ($form->get('data')->getData() != null) {
                
                $photos = $form->get('data')->getData();

                // On vérifie s'il y a une ou plusieurs photos
                if (is_array($photos)) {
                    foreach ($photos as $photographie) {
                        $photo = new Photo();
                        $postPhoto = new PostPhoto();
                        $photo->setDonneesPhoto(file_get_contents($photographie->getRealPath()));
                        $postPhoto->setPhotoId($photo);
                        $postPhoto->setPostId($post);
                        $entityManager->persist($postPhoto);

                        // On récupère le ou les type(s) des photos
                        $type = $photographie->getMimeType();

                        // On ajoute un nouveau format pour la photo si il n'existe pas sinon on récupère le format existant et on attribue à la photo l'id du format
                        $format = $entityManager->getRepository(Format::class)->findOneBy(['nom' => $type]);
                        if ($format === null) {
                            $format = new Format();
                            $format->setNom($type);
                            $entityManager->persist($format);
                            $photo->setFormatId($format);
                        } else {
                            $photo->setFormatId($format);
                        }

                        $entityManager->persist($photo);

                    }
                } else {
                    $photo = new Photo();
                    $postPhoto = new PostPhoto();
                    $photo->setDonneesPhoto(file_get_contents($form->get('data')->getData()));
                    $postPhoto->setPhotoId($photo);
                    $postPhoto->setPostId($post);
                    $entityManager->persist($postPhoto);

                    // On récupère le type de la photo
                    $type = $form->get('data')->getData()->getMimeType();

                    // On ajoute un nouveau format pour la photo si il n'existe pas sinon on récupère le format existant et on attribue à la photo l'id du format
                    $format = $entityManager->getRepository(Format::class)->findOneBy(['nom' => $type]);
                    if ($format === null) {
                        $format = new Format();
                        $format->setNom($type);
                        $entityManager->persist($format);
                        $photo->setFormatId($format);
                    } else {
                        $photo->setFormatId($format);
                    }

                    $entityManager->persist($photo);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {

        // utilisateur connecté
        $user = $this->getUser();

        // On récupère le compte de l'utilisateur connecté
        $compte = $entityManager->getRepository(Compte::class)->findOneBy(['id' => $user]);

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {

            try {
                // Il faut supprimer les photos associées au post
                $photos = $entityManager->getRepository(PostPhoto::class)->findBy(['post_id' => $post]);
                foreach ($photos as $photo) {
                    $entityManager->remove($photo);
                }
                
                $entityManager->remove($post);
                $entityManager->flush();
            } catch (\Exception $e) {
                return $this->redirectToRoute('app_compte_show', ['id' => $compte->getId()], Response::HTTP_SEE_OTHER);
            }

        }

        return $this->redirectToRoute('app_compte_show', ['id' => $compte->getId()], Response::HTTP_SEE_OTHER);
    }

    // On crée une route pour liker un post
    #[Route('/like/{id}', name: 'app_post_like', methods: ['GET'])]
    public function like(Post $post, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $compte = $entityManager->getRepository(Compte::class)->findOneBy(['id' => $user]);
        // On récupère le like associé au post et au compte
        $like = $entityManager->getRepository(Like::class)->findOneBy(['post_id' => $post, 'compte_id' => $compte]);

        if ($like === null) {
            $nouveauLike = new Like();
            $nouveauLike->setGolden(false);
            $nouveauLike->setPostId($post);
            $nouveauLike->setCompteId($compte);
            $entityManager->persist($nouveauLike);
        }
        $entityManager->flush();

        // On renvoie le nombre de like à l'aide de la fonction count
        $likes = count($entityManager->getRepository(Like::class)->findBy(['post_id' => $post]));

        return new JsonResponse(['nb_like' => $likes]);
    }

    // On crée une route pour unliker un post
    #[Route('/unlike/{id}', name: 'app_post_unlike', methods: ['GET'])]
    public function unlike(Post $post, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $compte = $entityManager->getRepository(Compte::class)->findOneBy(['id' => $user]);
        // On récupère le like associé au post et au compte
        $like = $entityManager->getRepository(Like::class)->findOneBy(['post_id' => $post, 'compte_id' => $compte]);

        if ($like !== null) {
            $entityManager->remove($like);
        }
        $entityManager->flush();

        // On renvoie le nombre de like à l'aide de la fonction count
        $likes = count($entityManager->getRepository(Like::class)->findBy(['post_id' => $post]));

        return new JsonResponse(['nb_like' => $likes]);
    }

}
