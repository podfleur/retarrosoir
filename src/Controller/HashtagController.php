<?php

namespace App\Controller;

use App\Entity\Hashtag;
use App\Entity\Post;
use App\Entity\Like;
use App\Entity\PostPhoto;
use App\Entity\PostHashtag;
use App\Form\HashtagType;
use App\Repository\HashtagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hashtag')]
class HashtagController extends AbstractController
{
    #[Route('/', name: 'app_hashtag_index', methods: ['GET'])]
    public function index(HashtagRepository $hashtagRepository): Response
    {
        return $this->render('hashtag/index.html.twig', [
            'hashtags' => $hashtagRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hashtag_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hashtag = new Hashtag();
        $form = $this->createForm(HashtagType::class, $hashtag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hashtag);
            $entityManager->flush();

            return $this->redirectToRoute('app_hashtag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hashtag/new.html.twig', [
            'hashtag' => $hashtag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hashtag_show', methods: ['GET'])]
    public function show(Hashtag $hashtag, EntityManagerInterface $em): Response
    {

        // On récupère tous les posts contenant le hashtag
        $posts = $em->getRepository(PostHashtag::class)->findBy(['hashtag_id' => $hashtag]);

        // On trie allPost par date de création décroissante
        usort($posts, function($a, $b) {
            return $b->getDatePublication() <=> $a->getDatePublication();
        });

        $postsWithPhotos = [];
        $allPhotoProfils = [];

        foreach ($posts as $post) {
            // Récupérer le post
            $post = $post->getPostId();

            // Récupérer la photo de profil pour chaque post
            $photoProfil = $post->getCompteId()->getPhotoId() ? base64_encode(stream_get_contents($post->getCompteId()->getPhotoId()->getDonneesPhoto())) : null;
            $formatPhotoProfil = $post->getCompteId()->getPhotoId() ? $post->getCompteId()->getPhotoId()->getFormatId()->getNom() : null;

            $postPhotos = [];
            $postPhotosEntities = $em->getRepository(PostPhoto::class)->findBy(['post_id' => $post]);

            foreach ($postPhotosEntities as $postPhoto) {
                $photo = $postPhoto->getPhotoId();
                $donneesPhoto = base64_encode(stream_get_contents($photo->getDonneesPhoto()));
                $format = $photo->getFormatId()->getNom();
                $postPhotos[] = [
                    'donneesPhoto' => $donneesPhoto,
                    'format' => $format,
                ];
            }

            // Ajoutez à la liste des posts avec leurs photos
            $postsWithPhotos[] = [
                'post' => $post,
                'photos' => $postPhotos,
                'nb_likes' => count($em->getRepository(Like::class)->findBy(['post_id' => $post])),
                'compte' => $post->getCompteId(),
            ];

            $allPhotoProfils[] = [
                'photoProfil' => $photoProfil,
                'formatPhotoProfil' => $formatPhotoProfil,
                'id' => $post->getCompteId()->getId(),
            ];
        }

        $nbPost = count($posts);

        return $this->render('hashtag/show.html.twig', [
            'hashtag' => $hashtag,
            'posts' => $postsWithPhotos,
            'allPhotoProfils' => $allPhotoProfils,
            'nbPost' => $nbPost,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_hashtag_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hashtag $hashtag, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HashtagType::class, $hashtag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hashtag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hashtag/edit.html.twig', [
            'hashtag' => $hashtag,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hashtag_delete', methods: ['POST'])]
    public function delete(Request $request, Hashtag $hashtag, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hashtag->getId(), $request->request->get('_token'))) {
            $entityManager->remove($hashtag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hashtag_index', [], Response::HTTP_SEE_OTHER);
    }
}
