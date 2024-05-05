<?php

namespace App\Controller;

use App\DTO\Periode_DTO;
use App\DTO\Compte_DTO;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Compte;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use App\Entity\Abonnement;

#[Route('/api')]
class APIController extends AbstractController
{
    #[Route('/GetAllAccountsInformations', name: 'app_api_get_accounts_informations', methods: ['GET'])]
    #[OA\Tag(name: 'Stat_Compte')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'integer'
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: Periode_DTO::class)
    )]
    public function GetNombreCreationsCompte(EntityManagerInterface $entityManager): JsonResponse
    {

        $comptes = $entityManager->getRepository(Compte::class)
        ->findAll(); // Récupère tous les comptes

        // Pour chaque compte, on va construire un objet Compte_DTO qui contiendra les informations nécessaires
        $compteDTO = [];

        foreach ($comptes as $compte) {
            $nombreAbonnes = count($entityManager->getRepository(Abonnement::class)->getAbonnes($compte->getId()));
            $nombreAbonnements = count($entityManager->getRepository(Abonnement::class)->getAbonnements($compte->getId()));
            $nombrePublications = count($entityManager->getRepository(Post::class)->getPublications($compte->getId()));

            $compteDTO[] = [
                'username' => $compte->getUsername(),
                'nombre_abonnes' => $nombreAbonnes,
                'nombre_abonnements' => $nombreAbonnements,
                'nombre_publications' => $nombrePublications,
                'date_creation' => $compte->getDateCreation()
            
            ];
        }

        return $this->json($compteDTO, Response::HTTP_OK); // Retourne tous les comptes
    }

}