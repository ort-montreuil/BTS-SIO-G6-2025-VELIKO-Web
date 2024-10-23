<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $userConnecte = $this->getUser();
        $form = $this->createForm(ProfilFormType::class, $userConnecte);


        return $this->render('profil/profil.html.twig', [
            'controller_name' => 'ProfilController',
            'titre' => 'Page de Profil',
            'profilForm' => $form

        ]);
    }

    #[Route('/profil/modification/{id}', name: 'app_profil_modification', methods: ['GET', 'POST'])]
    public function modificationProfil(User $user, Request $request, EntityManagerInterface $manager): Response
    {
        //Verifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_profil');
        }

        //Verifier si l'utilisateur connecté est le meme que celui qu'il veut modifié
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_profil');
        }

        $form = $this->createForm(ProfilFormType::class, $user);
        $form->handleRequest($request);


        //modification des infos si tout est ok
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations de votre compte ont bien été modifiées'
            );
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/modificationProfil.html.twig', [
            'controller_name' => 'ProfilController',
            'titre' => 'Page de modification Profil',
            'form' => $form->createView()
        ]);
    }

}