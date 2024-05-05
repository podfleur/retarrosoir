<?php

namespace App\DTO;

use DateTime;
use OpenApi\Attributes as OA;

#[OA\Schema(title: "Periode_DTO", description: "Periode DTO")]
class Periode_DTO
{
    #[OA\Property(description: "dateDebut")]
    public DateTime $dateDebut;
    
    #[OA\Property(description: "dateFin")]
    public DateTime $dateFin;
}
