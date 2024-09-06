<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelloController extends AbstractController
{
    #[Route('/hello')]

    public function hello(): Response{

        $hello = "Hello world";

        return $this->render('hello/hello.html.twig', [
            'titre' => $hello,
            ]);
    }

    #[Route('/coucou')]

    public function coucou(): Response
    {

        $coucou = "Coucou world";

        return new Response(
            '<html><h1>' . $coucou . '</h1></html>'
        );
    }


}