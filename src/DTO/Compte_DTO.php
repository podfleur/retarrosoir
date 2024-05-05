<?php

namespace App\DTO;
use OpenApi\Attributes as OA;

#[OA\Schema(title: "Compte_DTO", description: "Compte DTO")]
class Compte_DTO
{
    #[OA\Property(description: "username")]
    public string $username;
    
    #[OA\Property(description: "nombre_abonnes")]
    public int $nombre_abonnes;

    #[OA\Property(description: "nombre_abonnements")]
    public int $nombre_abonnements;

    #[OA\Property(description: "nombre_publications")]
    public int $nombre_publications;

    #[OA\Property(description: "date_creation")]
    public \Datetime $date_creation;
}
