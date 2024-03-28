<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Abonnement;
use App\Form\CompteType;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/compte')]
class CompteController extends AbstractController
{
    #[Route('/', name: 'app_compte_index', methods: ['GET'])]
    public function index(CompteRepository $compteRepository): Response
    {
        return $this->render('compte/index.html.twig', [
            'comptes' => $compteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_compte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte); 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $compte->setUsername($compte->getEmail());
            $compte->setPassword($hasher->hashPassword($compte, $compte->getPassword()));
            $compte->setRoles(['ROLE_USER']);

            $entityManager->persist($compte);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('compte/new.html.twig', [
            'compte' => $compte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_compte_show', methods: ['GET'])]
    public function show(Compte $compte, EntityManagerInterface $em): Response
    {
        // Vérifier si le compte connecté est abonné au compte affiché uniquement si le compte n'est pas le sien
        $user = $this->getUser();
        $abonne = false;
    
        if ($user !== null && $compte !== $user) {
            $abonnement = $em->getRepository(Abonnement::class)->findOneBy([
                'suiveur_id' => $user,
                'suivi_personne_id' => $compte
            ]);
    
            if ($abonnement !== null) {
                // Si un abonnement est trouvé, alors le compte connecté est abonné au compte affiché
                $abonne = true;
            }
        }
        
        return $this->render('compte/show.html.twig', [
            'compte' => $compte,
            'abonne' => $abonne,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_compte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Compte $compte, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        // On doit récupérer le nouveau mot de passe

        if ($form->isSubmitted() && $form->isValid()) {

            // Il faut modifier le mot de passe en le hashant
            $compte->setPassword($hasher->hashPassword($compte, $compte->getPassword()));

            $entityManager->flush();

            return $this->redirectToRoute('app_compte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('compte/edit.html.twig', [
            'compte' => $compte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_compte_delete', methods: ['POST'])]
    public function delete(Request $request, Compte $compte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$compte->getId(), $request->request->get('_token'))) {
            $entityManager->remove($compte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_compte_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/abonnements/{id}', name: 'app_compte_abonnements', methods: ['GET'])]
    public function abonnements(int $id, EntityManagerInterface $em): Response
    {
        // Récupérer le compte à partir de l'ID
        $compte = $em->getRepository(Compte::class)->find($id);

        // Vérifier si le compte a été trouvé
        if (!$compte) {
            throw $this->createNotFoundException('Compte non trouvé');
        }

        // Récupérer les abonnements du compte
        $abonnements = $em->getRepository(Abonnement::class)->findBy(['suiveur_id' => $compte]);

        return $this->render('compte/abonnements.html.twig', [
            'abonnements' => $abonnements,
        ]);
    }


}
