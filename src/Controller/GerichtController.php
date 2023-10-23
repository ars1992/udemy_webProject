<?php

namespace App\Controller;

use App\Entity\Gericht;
use App\Form\GerichtType;
use App\Repository\GerichtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gericht', name: 'gericht')]
class GerichtController extends AbstractController
{
    #[Route('/', name: 'bearbeiten')]
    public function index(GerichtRepository $gerichtRepository): Response
    {
        $gerichte = $gerichtRepository->findAll();
        return $this->render('gericht/index.html.twig', [
            'gerichte' => $gerichte,
        ]);
    }

    #[Route('/anlegen', name: 'anlegen')]
    public function anlegen(Request $request, EntityManagerInterface $manager): Response
    {
        $gericht = new Gericht();
        $form = $this->createForm(GerichtType::class, $gericht);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $manager->persist($gericht);
            $manager->flush();

            return $this->redirect($this->generateUrl('gerichtbearbeiten'));
        }

        return $this->render('gericht/anlegen.html.twig', [
            'anlegenForm' => $form->createView(),
        ]);
    }

    #[Route('/entfernen{id}', name: 'entfernen')]
    public function entfernen($id, GerichtRepository $gerichtRepository, EntityManagerInterface $manager): Response
    {
        $gericht = $gerichtRepository->find($id);
        $manager->remove($gericht);
        $manager->flush();

        $this->addFlash("erfolg", "Gericht wurde erfolgreich entfernt");

        return $this->redirect($this->generateUrl('gerichtbearbeiten'));
    }
}
