<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, EnvoyerMailController $mail, TokenService $token): Response
    {

        //Bloquer l'accès à la page d'inscription si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profil');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

          //  $token =  $token->generate();
            $verificationToken = $token->generate();
            $user->setVerificationToken($verificationToken);

            $entityManager->persist($user);
            $entityManager->flush();


            //On envoie un mail
            $mail->send(
                'no-reply@veliko.local',
                $user->getEmail(),
                'Activation de votre compte Veliko',
                'register',
                compact('user', 'verificationToken')
            );

            $this->addFlash('success', 'Un email de vérification vous a été envoyé. Veuillez vérifier votre boîte de réception.');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser(string $token, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Le token de vérification est invalide.');
        }

        $user->setVerified(true);
        //$user->setVerificationToken(null); // Retirer le token après vérification

        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été vérifié avec succès. Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('app_login');
    }
    #[Route('/conditions/utilisation', name: 'app_conditions_utilisation')]
    public function voirConditions(): Response
    {

        return $this->render('registration/conditionsGenerales.html.twig', [
        ]);
    }

}
