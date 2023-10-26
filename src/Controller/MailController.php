<?php

namespace App\Controller;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    #[Route('/mail', name: 'mail')]
    public function sendMail(MailerInterface $mailerInterface, Request $request): Response
    {
        $emailForm = $this->createFormBuilder()
            ->add("nachricht", TextareaType::class, [
                "attr" => array("rows" => 5)
            ])
            ->add("abschicken", SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-outline-danger float-right"
                ]
            ])
            ->getForm();

        $emailForm->handleRequest($request);

        if ($emailForm->isSubmitted()) {
            $eingabe = $emailForm->getData();
            $tisch = "tisch1";
            $email = (new TemplatedEmail())
                ->from("tisch1@menukarte.wip")
                ->to("kellner@menukarte.wip")
                ->subject("bestellung")
                ->htmlTemplate("mail/mail.html.twig")
                ->context([
                    "tisch" => $tisch,
                    "text" => $eingabe["nachricht"]
                ]);
            $mailerInterface->send($email);
            $this->addFlash("nachricht", "Email versendet");
            return $this->redirect($this->generateUrl("mail"));
        }

        return $this->render("mail/index.html.twig", [
            "emailForm" => $emailForm->createView()
        ]);
    }
}
