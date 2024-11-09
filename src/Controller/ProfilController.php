<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifProfilFormType;
use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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

        $form = $this->createForm(ModifProfilFormType::class, $user);
        $form->handleRequest($request);


        //modification des infos si tout est ok
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $manager->persist($user);
            $manager->flush();


            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/modificationProfil.html.twig', [
            'controller_name' => 'ProfilController',
            'titre' => 'Page de modification Profil',
            'form' => $form->createView()
        ]);
    }

    #[Route('/profil/confirmation-suppression/{id}', name: 'app_profil_confirmation_suppression', methods: ['GET', 'POST'])]
    public function confirmationSuppression(User $user)
    {
        return $this->render('profil/supprimerProfil.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @throws \Exception
     */
    #[Route('/profil/suppression/{id}', name: 'app_profil_suppression', methods: ['POST'])]
    public function supprimerProfil(User $user, EntityManagerInterface $manager, TokenStorageInterface $tokenStorage)
    {

        // Générer un mdp
        $randomNumber = random_int(0, 99999);
        $randomLettre = chr(random_int(97, 122));
        $randomMdp = str_shuffle($randomLettre . $randomNumber);

        // Rendre anonyme les champs sensibles
        $user->setEmail('anonymous' . $randomNumber . '@veliko.local');
        $user->setNom('anonymous');
        $user->setPrenom('anonymous');
        $user->setAdresse('');
        $user->setVerified(false);
        $user->setVerificationToken(null);
        $user->setPassword(password_hash($randomMdp, PASSWORD_BCRYPT));

        // Sauvegarder les modifications
        $manager->persist($user);
        $manager->flush();


        // Déconnecter l'utilisateur en supprimant son token
        $tokenStorage->setToken(null);


        // Rediriger vers la page de connexion après suppression
        return $this->redirectToRoute('app_login');
    }
}