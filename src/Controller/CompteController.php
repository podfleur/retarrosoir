<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Abonnement;
use App\Entity\Signalement;
use App\Entity\Commentaire;
use App\Entity\Photo;
use App\Entity\Format;
use App\Entity\Post;
use App\Entity\Like;
use App\Entity\PostPhoto;
use App\Form\CompteType;
use App\Form\UpdateCompteType;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\SecurityBundle\Security;

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

            $compte->setUsername($compte->getUsername());
            $compte->setPassword($hasher->hashPassword($compte, $compte->getPassword()));
            $compte->setRoles(['ROLE_USER']);
            $compte->setSuspendu(false);

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
        $donneesPhoto = null;
        $format = null;

        // Je dois récupérer la photo de profil
        $photo = $compte->getPhotoId();

        if ($photo) {
            $format = $photo->getFormatId();
            $donneesPhoto = stream_get_contents($photo->getDonneesPhoto());
        }
    
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
            'donneesPhoto' => $donneesPhoto != null ? base64_encode($donneesPhoto) : $donneesPhoto,
            'format' => $format != null ? $format->getNom() : $format
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_compte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Compte $compte, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UpdateCompteType::class, $compte);
        $form->handleRequest($request);

        // On doit vérifier que le compte à modifier est le même que celui connecté
        if ($compte !== $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // On doit récupérer le nouveau mot de passe

        if ($form->isSubmitted() && $form->isValid()) {

            // Il faut modifier la photo de profil si elle a été modifiée
            if ($form->get('data')->getData() != null) {
                
                // Vérification si le compte a été supprimé pendant l'édition
                if (!$entityManager->contains($compte)) {
                    // Redirection vers la page de connexion si le compte a été supprimé
                    return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
                }

                $photo = $compte->getPhotoId();
                if ($photo !== null) { // Vérifier si la photo est définie
                    $photo->setDonneesPhoto(file_get_contents($form->get('data')->getData()));
                    $entityManager->persist($photo);

                } else {
                    // La photo n'est pas définie, vous devez la créer
                    $photo = new Photo();
                    $photo->setDonneesPhoto(file_get_contents($form->get('data')->getData()));
                    $entityManager->persist($photo);
                    $compte->setPhotoId($photo);
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

                $compte->setPhotoId($photo);
                
            }

            // Il faut modifier le mot de passe en le hashant
            $compte->setPassword($hasher->hashPassword($compte, $compte->getPassword()));

            $entityManager->flush();


            return $this->redirectToRoute('app_compte_show', ['id' => $compte->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('compte/edit.html.twig', [
            'compte' => $compte,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_compte_delete', methods: ['POST'])]
    public function delete(Request $request, Compte $compte, EntityManagerInterface $entityManager, Security $security): Response
    {
        if ($this->isCsrfTokenValid('delete'.$compte->getId(), $request->request->get('_token'))) {

            // On supprime tous les abonnements du compte
            $abonnements = $entityManager->getRepository(Abonnement::class)->findBy(['suiveur_id' => $compte]);
            foreach ($abonnements as $abonnement) {
                $entityManager->remove($abonnement);
            }

            // On supprime tous les abonnements au compte
            $abonnements = $entityManager->getRepository(Abonnement::class)->findBy(['suivi_personne_id' => $compte]);
            foreach ($abonnements as $abonnement) {
                $entityManager->remove($abonnement);
            }

            // On supprime tous les signalements du compte
            $signalements = $entityManager->getRepository(Signalement::class)->findBy(['signale_id' => $compte]);
            foreach ($signalements as $signalement) {
                $entityManager->remove($signalement);
            }

            // On supprime tous les posts du compte
            $posts = $entityManager->getRepository(Post::class)->findBy(['compte_id' => $compte]);
            foreach ($posts as $post) {
                $postPhotos = $entityManager->getRepository(PostPhoto::class)->findBy(['post_id' => $post]);
                foreach ($postPhotos as $postPhoto) {
                    $entityManager->remove($postPhoto);
                }
                
                $postHashtags = $entityManager->getRepository(PostHashtag::class)->findBy(['post_id' => $post]);
                foreach ($postHashtags as $postHashtag) {
                    $entityManager->remove($postHashtag);
                }
                
                $commentaires = $entityManager->getRepository(Commentaire::class)->findBy(['post_id' => $post]);
                foreach ($commentaires as $commentaire) {
                    $entityManager->remove($commentaire);
                }

                $likes = $entityManager->getRepository(Like::class)->findBy(['post_id' => $post]);
                foreach ($likes as $like) {
                    $entityManager->remove($like);
                }

                $signalements = $entityManager->getRepository(Signalement::class)->findBy(['post_id' => $post]);
                foreach ($signalements as $signalement) {
                    $entityManager->remove($signalement);
                }
                
                $entityManager->remove($post);
            }

            // On supprime tous les likes du compte
            $likes = $entityManager->getRepository(Like::class)->findBy(['compte_id' => $compte]);
            foreach ($likes as $like) {
                $entityManager->remove($like);
            }

            // On supprime tous les signalements
            $signalements = $entityManager->getRepository(Signalement::class)->findBy(['signaleur_id' => $compte]);
            $signalements += $entityManager->getRepository(Signalement::class)->findBy(['signale_id' => $compte]);
            foreach ($signalements as $signalement) {
                $entityManager->remove($signalement);
            }

            // On supprime tous les commentaires du compte
            $commentaires = $entityManager->getRepository(Commentaire::class)->findBy(['compte_id' => $compte]);
            $commentaires += $entityManager->getRepository(Commentaire::class)->findBy((['post_id' => $compte->getId()]));
            foreach ($commentaires as $commentaire) {
                $entityManager->remove($commentaire);
            }

            // On supprime la photo de profil
            $photo = $compte->getPhotoId();
            if ($photo) {
                $entityManager->remove($photo);
            }

            // On supprime tous les commentaires du compte
            $commentaires = $entityManager->getRepository(Commentaire::class)->findBy(['compte_id' => $compte]);
            foreach ($commentaires as $commentaire) {
                $entityManager->remove($commentaire);
            }
            
            $entityManager->remove($compte);
            $entityManager->flush();
            
            // On déconnecte le compte
            $response = $security->logout(false);

        }

        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }

    // Je veux créer une route pour s'abonner à un compte
    #[Route('/subscribe/{id}', name: 'app_subscribe')]
    public function subscribe($id, EntityManagerInterface $em, UserInterface $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $compte = $user;

        $compteToSubscribe = $em->getRepository(Compte::class)->find($id);

        if (!$compteToSubscribe) {
            throw $this->createNotFoundException('Compte non trouvé');
        }

        $abonnement = new Abonnement();
        $abonnement->setSuiveurId($compte);
        $abonnement->setSuiviPersonneId($compteToSubscribe);

        $em->persist($abonnement);
        $em->flush();

        return $this->redirectToRoute('app_compte_show', ['id' => $id]);
    }

    // Je veux créer une route pour se désabonner d'un compte
    #[Route('/unsubscribe/{id}', name: 'app_unsubscribe')]
    public function unsubscribe($id, EntityManagerInterface $em, UserInterface $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $compte = $user;

        $compteToUnsubscribe = $em->getRepository(Compte::class)->find($id);

        if (!$compteToUnsubscribe) {
            throw $this->createNotFoundException('Compte non trouvé');
        }

        $abonnement = $em->getRepository(Abonnement::class)->findOneBy([
            'suiveur_id' => $compte,
            'suivi_personne_id' => $compteToUnsubscribe
        ]);

        if (!$abonnement) {
            throw $this->createNotFoundException('Abonnement non trouvé');
        }

        $em->remove($abonnement);
        $em->flush();

        return $this->redirectToRoute('app_compte_show', ['id' => $id]);
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

    // Route pour signaler un compte
    #[Route('/signaler/{id}', name: 'app_compte_signaler', methods: ['GET'])]
    public function signaler($id, EntityManagerInterface $em, Request $request): Response
    {
        $compte = $em->getRepository(Compte::class)->find($id);

        // Il faut ajouter un nouveau signalement
        $signalement = new Signalement();
        $signalement->setSignaleurId($this->getUser());
        $signalement->setSignaleId($compte);

        $motif = $request->query->get('reportArea');
        $signalement->setMotif($motif);

        $em->persist($signalement);
        $em->flush();

        // On vérifie que le compte n'a pas eu 10 signalement sinon il faut le suspendre
        $nbSignalements = count($em->getRepository(Signalement::class)->findBy(['signale_id' => $compte]));

        if ($nbSignalements >= 10) {
            $compte->setSuspendu(true);
            $em->flush();
        }

        return $this->redirectToRoute('app_compte_show', ['id' => $id]);
    }


}
