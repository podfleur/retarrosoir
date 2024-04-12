<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Hashtag;
use App\Entity\Compte;
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
use Symfony\Component\Security\Core\User\UserInterface;

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
            return $b->getId() <=> $a->getId();
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

        // On vérifie si le compte connecté est abonné au hashtag
        $compte = $this->getUser();

        $abonne = false;

        $abonnement = $em->getRepository(Abonnement::class)->findOneBy([
            'suiveur_id' => $compte,
            'suivi_hashtag_id' => $hashtag
        ]);

        if ($abonnement) {
            $abonne = true;
        }

        return $this->render('hashtag/show.html.twig', [
            'hashtag' => $hashtag,
            'posts' => $postsWithPhotos,
            'allPhotoProfils' => $allPhotoProfils,
            'nbPost' => $nbPost,
            'abonne' => $abonne
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

    #[Route('/hashtag-subscribe/{id}', name: 'app_hashtag_subscribe')]
    public function subscribe($id, EntityManagerInterface $em, UserInterface $user, Hashtag $hashtag): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!$hashtag) {
            throw $this->createNotFoundException('Compte non trouvé');
        }

        $abonnement = new Abonnement();
        $abonnement->setSuiveurId($user);
        $abonnement->setSuiviHashtagId($hashtag);

        $em->persist($abonnement);
        $em->flush();

        return $this->redirectToRoute('app_hashtag_show', ['id' => $id]);
    }

    // Je veux créer une route pour se désabonner d'un compte
    #[Route('/etablissement-unsubscribe/{id}', name: 'app_hashtag_unsubscribe')]
    public function unsubscribe($id, EntityManagerInterface $em, UserInterface $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $compte = $user;

        $compteToUnsubscribe = $em->getRepository(Compte::class)->find($compte);

        if (!$compteToUnsubscribe) {
            throw $this->createNotFoundException('Compte non trouvé');
        }

        $abonnement = $em->getRepository(Abonnement::class)->findOneBy([
            'suiveur_id' => $compte,
            'suivi_hashtag_id' => $id
        ]);

        if (!$abonnement) {
            throw $this->createNotFoundException('Abonnement non trouvé');
        }

        $em->remove($abonnement);
        $em->flush();

        return $this->redirectToRoute('app_hashtag_show', ['id' => $id]);
    }
}
