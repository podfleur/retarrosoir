<?php

namespace App\Controller;

use App\DTO\Periode_DTO;
use OpenApi\Attributes\Post;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Route('/api')]
class APIController extends AbstractController
{
    #[Route('/GetNombreCreationsCompte', name: 'app_api', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'integer'
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new Model(type:Periode_DTO::class)
    )]
    public function GetNombreCreationsCompte(#[MapRequestPayload] Periode_DTO $periode_DTO): Response
    {
        return $this->json(12);
    }

    #[Route('/RetourneUnePeriodeAleatoire', name:'app_api_periode', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type:Periode_DTO::class)
    )]
    public function RetourneUnePeriodeAleatoire(): Response
    {
        $periode = new Periode_DTO();
        $periode->dateDebut = new \DateTime();
        $periode->dateFin = new \DateTime();
        return $this->json($periode);
    }
}