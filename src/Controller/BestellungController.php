<?php

namespace App\Controller;

use App\Entity\Bestellung;
use App\Entity\Gericht;
use App\Repository\BestellungRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BestellungController extends AbstractController
{
    #[Route('/bestellung', name: 'bestellung')]
    public function index(BestellungRepository $bestellungRepository): Response
    {
        $bestellungen = $bestellungRepository->findBy([
            "tisch" => "Tisch1",
        ]);
        
        return $this->render('bestellung/index.html.twig', [
            'bestellungen' => $bestellungen,
        ]);
    }

    #[Route('/bestellen/{id}', name: 'bestellen')]
    public function bestellen(Gericht $gericht, EntityManagerInterface $manager): Response
    {
        $bestellung = (new Bestellung)
            ->setTisch("Tisch1")
            ->setName($gericht->getName())
            ->setBestellnummer($gericht->getId())
            ->setPreis($gericht->getPreis())
            ->setStatus("offen");

        $manager->persist($bestellung);
        $manager->flush();

        $this->addFlash("bestell", $bestellung->getName() . " wurde zu Bestellung hinzugefügt.");

        return $this->redirect($this->generateUrl('menu'));
    }

    #[Route('/status/{id},{status}', name: 'status')]
    public function status($id, $status, EntityManagerInterface $manager): Response
    {
        $bestellung = $manager->getRepository(Bestellung::class)->find($id);
        $bestellung->setStatus($status);
        $manager->flush();

        return $this->redirect($this->generateUrl("bestellung"));
    }

    #[Route('/löschen/{id}', name: 'löschen')]
    public function löschen($id, BestellungRepository $bestellungRepository, EntityManagerInterface $manager): Response
    {
        $bestellung = $bestellungRepository->find($id);
        $manager->remove($bestellung);
        $manager->flush();

        $this->addFlash("erfolg", "bestellung wurde erfolgreich entfernt");

        return $this->redirect($this->generateUrl('bestellung'));
    }
}
