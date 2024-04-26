<?php

namespace App\DTO;
use OpenApi\Attributes as OA;

#[OA\Schema(title: "Periode_DTO", description: "Periode DTO")]
class Periode_DTO
{
    #[OA\Property(description: "Date de début")]
    public \DateTime $dateDebut;
    #[OA\Property(description: "Date de fin")]
    public \DateTime $dateFin;
}
