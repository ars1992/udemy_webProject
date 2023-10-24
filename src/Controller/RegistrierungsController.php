<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrierungsController extends AbstractController
{
    #[Route('/reg', name: 'reg')]
    public function reg(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $regform = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'label' => 'Mitarbeiter'
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Passwort'],
                'second_options' => ['label' => 'Passwort Wiederholen']
            ])

            ->add('registrieren', SubmitType::class)
            ->getForm();

        $regform->handleRequest($request);
        
        if ($regform->isSubmitted() && $regform->isValid()) { 
            $eingabe = $regform->getData();
            $user = new User();
            $user->setUsername($eingabe["username"]);
            $user->setPassword($userPasswordHasher->hashPassword($user, $eingabe["password"]));

            $manager->persist($user);
            $manager->flush();
            return $this->redirect($this->generateUrl('gerichtbearbeiten'));
        }
        

        return $this->render('registrierungs/index.html.twig', [
            'regform' => $regform->createView(),
        ]);
    }
}
