<?php

namespace App\Controller;

use App\Entity\Gericht;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gericht', name: 'app_gericht')]
class GerichtController extends AbstractController
{
    #[Route('/', name: 'bearbeiten')]
    public function index(): Response
    {
        return $this->render('gericht/index.html.twig', [
            'controller_name' => 'GerichtController',
        ]);
    }

    #[Route('/anlegen', name: 'anlegen')]
    public function anlegen(Request $request, ManagerRegistry $manager): Response
    {
        $gericht = new Gericht();
        $gericht->setName("Pizza");

        $entityManager = $manager->getManager();
        $entityManager->persist($gericht);
        $entityManager->flush();



    }
}
