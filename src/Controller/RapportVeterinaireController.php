<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RapportVeterinaireController extends AbstractController
{
    #[Route('/rapport/veterinaire', name: 'app_rapport_veterinaire')]
    public function index(): Response
    {
        return $this->render('rapport_veterinaire/index.html.twig', [
            'controller_name' => 'RapportVeterinaireController',
        ]);
    }
}
