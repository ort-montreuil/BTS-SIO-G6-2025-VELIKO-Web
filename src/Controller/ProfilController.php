<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $userConnecte = $this->getUser();
        $form = $this->createForm(ProfilFormType::class, $userConnecte);


        if ($userConnecte instanceof User){
            $nom = $userConnecte-> getNom();
            //var_dump($userConnecte);
        }


        return $this->render('profil/profil.html.twig', [
            'controller_name' => 'ProfilController',
            'titre' => 'Page de Profil',
            'profilForm' => $form,
            'nom' => $nom

        ]);
    }

}