<?php

// src/Controller/ReservationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\ReservationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reserver/{id}', name: 'app_reservation')]
    public function reserverVelo(UserRepository $userRepository, int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Redirige l'utilisateur vers la page de connexion
        }

        //Verifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_map');
        }
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_map');
        }

        $form = $this->createForm(ReservationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $reservation = $form->getData();
            $reservation->setIdStationDepart($form->get('idStationDepart')->getData()->getId());
            $reservation->setIdStationArrivee($form->get('idStationArrivee')->getData()->getId());
            $reservation->setEmailUser($this->getUser()->getEmail());
            $manager->persist($reservation);
            $manager->flush();

            $this->addFlash('success', 'Votre réservation a été effectuée avec succès !');
            return $this->redirectToRoute('app_map');
        }

        return $this->render('reservation/reserver.html.twig', [
            'reservationForm' => $form->createView(),
        ]);
    }
}
