<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Compte;
use App\Entity\Hashtag;
use App\Entity\Abonnement;
use App\Entity\Etablissement;
use App\Entity\Post;
use App\Entity\Photo;
use App\Entity\Format;
use App\Entity\PostPhoto;
use App\Entity\Like;
use App\Entity\Commentaire;
use App\Entity\Signalement;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class HomeController extends AbstractController
{
    
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        // Vérifier si le compte connecté est abonné au compte affiché uniquement si le compte n'est pas le sien

        $compte = $this->getUser();
        $comptes = $em->getRepository(Compte::class)->findAll();

        // On parcourt tous les comptes pour récupérer leurs posts
        foreach ($comptes as $compte) {
            $posts = $em->getRepository(Post::class)->findBy(['compte_id' => $compte], ['date_publication' => 'DESC']);
            $compte->posts = $posts;
        }

        // Je dois récupérer la photo de profil
        $photo = $compte->getPhotoId();
        
        // On récupère les photos de posts associés au profil sachant que l'utilisateur est dans la table compte, les photos dans la tables photo, les posts dans la table post, les formats dans la table format et les photos de post dans la table post_photo
        $posts = $em->getRepository(Post::class)->findBy(['compte_id' => $compte], ['date_publication' => 'DESC']);
        $postsWithPhotos = [];

        foreach ($posts as $post) {

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
            ];
        }

        $nbPost = count($posts);


        return $this->render('home/index.html.twig', [
            'compte' => $compte,
            'posts' => $postsWithPhotos,
            'nbPost' => $nbPost,
        ]);
    }


    // Je veux créer une route pour rechercher un #, un compte ou un établissement
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $compte = $this->getUser();

        // Il me faut récupérer les données du formulaire pour pouvoir réaliser la recherche dans la base de données
        $search = $request->query->get('search');

        // Je veux récupérer les comptes qui correspondent à la recherche
        $comptes = $em->getRepository(Compte::class)->createQueryBuilder('c')
            ->where('c.username LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
        
        foreach ($comptes as $compte) {
            $photo = $compte->getPhotoId();
            if ($photo) {
                $compte->photo = [
                    'donneesPhoto' => base64_encode(stream_get_contents($photo->getDonneesPhoto())),
                    'format' => $photo->getFormatId()->getNom(),
                ];
            } else {
                $compte->photo = null;
            }

            $isSubscribed = $em->getRepository(Abonnement::class)->findOneBy([
                'suiveur_id' => $this->getUser(),
                'suivi_personne_id' => $compte 
            ]);
    
            // Ajouter une nouvelle propriété à chaque compte pour indiquer s'il est abonné
            $compte->isSubscribed = $isSubscribed ? true : false;
        }

        $hashtags = $em->getRepository(Hashtag::class)->createQueryBuilder('h')
            ->where('h.texte LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();

        $etablissements = $em->getRepository(Etablissement::class)->createQueryBuilder('e')
            ->where('e.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();


        return $this->render('home/search.html.twig', [
            'controller_name' => 'HomeController',
            'comptes' => $comptes,
            'hashtags' => $hashtags,
            'etablissements' => $etablissements,
        ]);
    }

    // Route pour l'autocompletion
    #[Route('/autocomplete', name: 'app_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request, EntityManagerInterface $em): Response
    {
        $search = $request->query->get('search');

        // Effectuez votre recherche pour obtenir les suggestions d'autocomplétion
        $suggestions = $em->getRepository(Compte::class)->createQueryBuilder('c')
            ->where('c.username LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();

        $suggestions += $em->getRepository(Hashtag::class)->createQueryBuilder('h')
            ->where('h.texte LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();

        $suggestions += $em->getRepository(Etablissement::class)->createQueryBuilder('e')
            ->where('e.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();

        // On veut garder uniquement les noms des comptes, hashtags et établissements
        $suggestions = array_map(function($suggestion) {
            return $suggestion->getUsername() ?? $suggestion->getTexte() ?? $suggestion->getNom();
        }, $suggestions);
        
        return new JsonResponse($suggestions);
    }

}
