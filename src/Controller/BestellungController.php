<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BestellungController extends AbstractController
{
    #[Route('/bestellung', name: 'app_bestellung')]
    public function index(): Response
    {
        return $this->render('bestellung/index.html.twig', [
            'controller_name' => 'BestellungController',
        ]);
    }
}
