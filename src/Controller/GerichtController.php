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


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function anlegen(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $gericht = new Gericht();
        $form = $this->createForm(GerichtType::class, $gericht);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var UploadedFile $bild */
            $bild = $form->get('bild')->getData();
            if ($bild) {
                $originalFilename = pathinfo($bild->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$bild->guessExtension();

                try {
                    $bild->move(
                        $this->getParameter('bilder_ordner'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return new Response($e);
                }
                $gericht->setBild($newFilename);
            }

            $manager->persist($gericht);
            $manager->flush();

            return $this->redirect($this->generateUrl('gerichtbearbeiten'));
        }

        return $this->render('gericht/anlegen.html.twig', [
            'anlegenForm' => $form->createView(),
        ]);
    }

    #[Route('/anzeigen/{id}', name: 'anzeigen')]
    public function anzeigen(Gericht $gericht): Response
    {
        return $this->render('gericht/anzeigen.html.twig', [
            'gericht' => $gericht,
        ]);
    }

    #[Route('/entfernen/{id}', name: 'entfernen')]
    public function entfernen($id, GerichtRepository $gerichtRepository, EntityManagerInterface $manager): Response
    {
        $gericht = $gerichtRepository->find($id);
        $manager->remove($gericht);
        $manager->flush();

        $this->addFlash("erfolg", "Gericht wurde erfolgreich entfernt");

        return $this->redirect($this->generateUrl('gerichtbearbeiten'));
    }
}
