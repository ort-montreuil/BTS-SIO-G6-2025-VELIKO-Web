<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelloController extends AbstractController
{
    #[Route('/', name: 'app_accueil' )]

    public function hello(): Response{

        $hello = "Hello world";

        return $this->render('hello/accueil.html.twig', [
            'titre' => $hello,
            ]);
    }

    #[Route('/a/propos}', name: 'app_apropos' )]
    public function voirAPropos(): Response{

        return $this->render('hello/aPropos.html.twig', [

        ]);
    }



}