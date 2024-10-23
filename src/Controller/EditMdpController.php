<?php

namespace App\Controller;

use App\Form\EditMdpFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class EditMdpController extends AbstractController
{
    #[Route('/edit/mdp/{id}', name: 'app_edit_mdp', methods: ['GET' , 'POST'])]
    public function modificationMdp(int $id, UserRepository $userRepository, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        // Trouver l'utilisateur par son ID
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_profil');
        }

        // Vérifier si l'utilisateur connecté est le même que celui à modifier (en comparant les ID)
        if ($this->getUser()->getId() !== $user->getId()) {
            return $this->redirectToRoute('app_profil');
        }

        $form = $this->createForm(EditMdpFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'ancien mot de passe est correct
            if ($hasher->isPasswordValid($user, $form->get('password')->getData())) {
                // Hash le nouveau mot de passe et l'enregistre
                $user->setPassword(
                    $hasher->hashPassword(
                        $user,
                        $form->get('newPassword')->getData()
                    )
                );

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le mot de passe a bien été modifié'
                );
                return $this->redirectToRoute('app_profil');
            } else {
                // Si l'ancien mot de passe est incorrect
                $this->addFlash(
                    'warning',
                    'Le mot de passe actuel est incorrect'
                );
            }
        }

        // Afficher le formulaire avec les messages d'erreur (le cas échéant)
        return $this->render('edit_mdp/editMdp.html.twig', [
            'controller_name' => 'EditMdpController',
            'form' => $form->createView()
        ]);
    }
}
