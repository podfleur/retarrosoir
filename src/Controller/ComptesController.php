<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComptesController extends AbstractController
{
    #[Route('/comptes', name: 'app_comptes')]
    public function index(): Response
    {
        return $this->render('comptes/index.html.twig', [
            'controller_name' => 'ComptesController',
        ]);
    }
}
