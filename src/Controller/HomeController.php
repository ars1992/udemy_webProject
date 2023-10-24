<?php

namespace App\Controller;

use App\Repository\GerichtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GerichtRepository $gerichtRepository): Response
    {
        $gerichte = $gerichtRepository->findAll();
        if(count($gerichte) > 1){
            $zufallsGerichte = array_rand($gerichte, count($gerichte));
            return $this->render('home/index.html.twig', [
                'gerichte' => array($gerichte[$zufallsGerichte[0]], $gerichte[$zufallsGerichte[1]])
            ]);
        }
        return $this->render('home/index.html.twig', [
            "gerichte" => array()
        ]);
    }

}
