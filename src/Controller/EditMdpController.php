<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditMdpFormType;
use App\Form\MdpOublieFormType;
use App\Repository\UserRepository;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
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



    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/envoieMailMdp', name: 'app_emailMdp', methods: ['GET' , 'POST'])]
// Fonction pour envoyer un mail de réinitialisation de mot de passe
    public function envoyer(Request $request, EnvoyerMailController $mail, EntityManagerInterface $entityManager, TokenService $token): Response
    {
        $email = $request->request->get('email');

        // Vérifier si l'email est fourni
        if (!$email) {
            $this->addFlash('error', 'Veuillez entrer un email valide.');
            return $this->redirectToRoute('app_renseignerEmail');
        }

        // Vérifier si l'utilisateur existe
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Si l'utilisateur n'existe pas, ajouter un message d'erreur
        if (!$user) {
            $this->addFlash('error', 'Aucun utilisateur trouvé avec cet email.');
            return $this->redirectToRoute('app_renseignerEmail');
        }

        // Générer un token de réinitialisation
        $verificationToken = $token->generate();
        $user->setVerificationToken($verificationToken);

        // dd($user->getVerificationToken());

        // Sauvegarder le token dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // Envoyer l'email avec le lien de réinitialisation
        $mail->send(
            'no-reply@veliko.local',
            $email,
            'Réinitialisation : Mot de passe oublié',
            'mdpOublie', // Nom du template Twig pour l'email
            ['verificationToken' => $verificationToken] // Passer le token au template
        );

        // Ajouter un message de succès et rediriger l'utilisateur
        $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
        return $this->redirectToRoute('app_login');
    }


    #[Route('/renseignerEmail', name: 'app_renseignerEmail', methods: ['GET' , 'POST'])]
    public function renseignerEmail(): Response
    {
        $this->redirectToRoute('app_login');

        //Afficher la page où l'utilisateur renseignera sons email pour reinitialiser son mdp
        return $this->render('edit_mdp/renseignerEmail.html.twig', []);

    }


    #[Route('/edit/mdpOublie/{token}', name: 'app_edit_mdpOublie', methods: ['GET', 'POST'])]
    public function modificationMdpOublie(string $token, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        // Vérifiez si le token est valide
        $user = $entityManager->getRepository(User::class)->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Le token de vérification est invalide.');
        }


        // Créez le formulaire de réinitialisation de mot de passe
        $form = $this->createForm(MdpOublieFormType::class);
        $form->handleRequest($request);

        // dd($form->isSubmitted(), $form->isValid());

        //dump($form->isSubmitted());
        // Traitez la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le mot de passe de l'utilisateur
            $user->setPassword(
                $hasher->hashPassword(
                    $user,
                    $form->get('newPassword')->getData()
                )

            );

            // Optionnel : Réinitialisez le token après l'utilisation
            // $user->setVerificationToken(null); // Réinitialisez ou supprimez le token si nécessaire

            //Flush les modifications
            $entityManager->persist($user);
            $entityManager->flush();
            //dd($user->getPassword());

            $this->addFlash('success', 'Le mot de passe a bien été modifié');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('edit_mdp/editMdpOublie.html.twig', [
            'token' => $token,
            'form' => $form->createView()
        ]);
    }


}
