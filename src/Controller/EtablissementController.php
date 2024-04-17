<?php

namespace App\Controller;

use App\Entity\Etablissement;
use App\Entity\Format;
use App\Entity\Photo;
use App\Entity\Compte;
use App\Entity\Post;
use App\Entity\Signalement;
use App\Entity\PostPhoto;
use App\Entity\Like;
use App\Entity\Abonnement;
use App\Form\EtablissementType;
use App\Repository\EtablissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/etablissement')]
class EtablissementController extends AbstractController
{
    #[Route('/', name: 'app_etablissement_index', methods: ['GET'])]
    public function index(EtablissementRepository $etablissementRepository): Response
    {
        return $this->render('etablissement/index.html.twig', [
            'etablissements' => $etablissementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_etablissement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        // On récupère l'URL dont on provient
        $referer = $request->headers->get('referer');

        $etablissement = new Etablissement();
        $form = $this->createForm(EtablissementType::class, $etablissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            if ($form->get('data')->getData() != null) {
                $photo = $etablissement->getPhotoId();
                if ($photo !== null) { // Vérifier si la photo est définie
                    $photo->setDonneesPhoto(file_get_contents($form->get('data')->getData()));
                    $entityManager->persist($photo);

                } else {
                    // La photo n'est pas définie, vous devez la créer
                    $photo = new Photo();
                    $photo->setDonneesPhoto(file_get_contents($form->get('data')->getData()));
                    $entityManager->persist($photo);
                    $etablissement->setPhotoId($photo);
                }

                // On récupère le type de la photo
                $type = $form->get('data')->getData()->getMimeType();
                    
                // On ajoute un nouveau format pour la photo si il n'existe pas sinon on récupère le format existant et on attribue à la photo l'id du format
                $format = $entityManager->getRepository(Format::class)->findOneBy(['nom' => $type]);

                if (!$format) {
                    $format = new Format();
                    $format->setNom($type);
                    $entityManager->persist($format);
                }

                $photo->setFormatId($format);
                $etablissement->setPhotoId($photo);
            }

            $entityManager->persist($etablissement);
            $entityManager->flush();

            return $this->redirectToRoute('app_etablissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etablissement/new.html.twig', [
            'etablissement' => $etablissement,
            'form' => $form,
            'referer' => $referer
        ]);
    }

    #[Route('/{id}', name: 'app_etablissement_show', methods: ['GET'])]
    public function show(Etablissement $etablissement, EntityManagerInterface $em): Response
    {
        $compte = $this->getUser();

        // S'il y a une photo, on la convertit en base64
        if ($etablissement->getPhotoId() !== null) {
            $donneesPhoto = base64_encode(stream_get_contents($etablissement->getPhotoId()->getDonneesPhoto()));
            $format = $etablissement->getPhotoId()->getFormatId()->getNom();
        } else {
            $donneesPhoto = null;
            $format = null;
        }

        $nbAbonnes = count($em->getRepository(Abonnement::class)->findBy(['suivi_etablissement_id' => $etablissement]));
        $nbCompteAssocie = count($em->getRepository(Compte::class)->findBy(['etablissement_id' => $etablissement]));

        $abonne = false;

        // On cherche à savoir si le compte connecté est abonné à l'établissement
        $abonnement = $em->getRepository(Abonnement::class)->findOneBy([
            'suiveur_id' => $compte,
            'suivi_etablissement_id' => $etablissement
        ]);

        if ($abonnement) {
            $abonne = true;
        }

        // On recherche l'ensemble des post créé par des utilisateurs dont etablissement_id est égal à l'id de l'établissement
        $comptes = $em->getRepository(Compte::class)->findBy(['etablissement_id' => $etablissement]);
        $posts = [];
        foreach ($comptes as $compte) {
            $posts = array_merge($posts, $em->getRepository(Post::class)->findBy(['compte_id' => $compte]));
        }

        $postsWithPhotos = [];
        $allPhotoProfils = [];

        foreach ($posts as $post) {

            $estSuspendu = $post->isSuspendu();

            if ($estSuspendu) {
                continue;
            }

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

        return $this->render('etablissement/show.html.twig', [
            'etablissement' => $etablissement,
            'donneesPhoto' => $donneesPhoto,
            'format' => $format,
            'compte' => $compte,
            'nbAbonnes' => $nbAbonnes,
            'nbCompteAssocie' => $nbCompteAssocie,
            'abonne' => $abonne,
            'posts' => $postsWithPhotos,
            'allPhotoProfils' => $allPhotoProfils,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_etablissement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etablissement $etablissement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtablissementType::class, $etablissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_etablissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etablissement/edit.html.twig', [
            'etablissement' => $etablissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etablissement_delete', methods: ['POST'])]
    public function delete(Request $request, Etablissement $etablissement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etablissement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($etablissement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etablissement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/associes/{id}', name: 'app_etablissement_associes', methods: ['GET'])]
    public function associes(Etablissement $etablissement, EntityManagerInterface $em): Response
    {
        $comptes = $em->getRepository(Compte::class)->findBy(['etablissement_id' => $etablissement]);

        return $this->render('etablissement/associes.html.twig', [
            'comptes' => $comptes,
            'etablissement' => $etablissement,
        ]);
    }

    #[Route('/etablissement-subscribe/{id}', name: 'app_etablissement_subscribe')]
    public function subscribe($id, EntityManagerInterface $em, UserInterface $user, Etablissement $etablissement): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (!$etablissement) {
            throw $this->createNotFoundException('Compte non trouvé');
        }

        $abonnement = new Abonnement();
        $abonnement->setSuiveurId($user);
        $abonnement->setSuiviEtablissementId($etablissement);

        $em->persist($abonnement);
        $em->flush();

        return $this->redirectToRoute('app_etablissement_show', ['id' => $id]);
    }

    // Je veux créer une route pour se désabonner d'un compte
    #[Route('/etablissement-unsubscribe/{id}', name: 'app_etablissement_unsubscribe')]
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
            'suivi_etablissement_id' => $id
        ]);

        if (!$abonnement) {
            throw $this->createNotFoundException('Abonnement non trouvé');
        }

        $em->remove($abonnement);
        $em->flush();

        return $this->redirectToRoute('app_etablissement_show', ['id' => $id]);
    }

}
