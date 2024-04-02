<?php

namespace App\Controller;

use App\Entity\Etablissement;
use App\Entity\Format;
use App\Entity\Photo;
use App\Form\EtablissementType;
use App\Repository\EtablissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        ]);
    }

    #[Route('/{id}', name: 'app_etablissement_show', methods: ['GET'])]
    public function show(Etablissement $etablissement): Response
    {
        return $this->render('etablissement/show.html.twig', [
            'etablissement' => $etablissement,
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
}
