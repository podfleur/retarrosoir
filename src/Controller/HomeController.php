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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        // Je veux renvoyer le compte de l'utilisateur connecté
        $compte = $this->getUser();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'compte' => $compte
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
        $comptes = $em->getRepository(Compte::class)->findBy(['username' => $search]);
        $hashtags = $em->getRepository(Hashtag::class)->findBy(['texte' => $search]);
        $etablissements = $em->getRepository(Etablissement::class)->findBy(['nom' => $search]);


        return $this->render('home/search.html.twig', [
            'controller_name' => 'HomeController',
            'comptes' => $comptes,
            'hashtags' => $hashtags,
            'etablissements' => $etablissements,
        ]);
    }

}
