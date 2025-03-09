<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Form\ReservationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    #[Route('/reserver/{id}', name: 'app_reservation')]
    public function reserverVelo(User $user, Request $request,EntityManagerInterface $manager): Response
    {
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
            'reservationForm' => $form,

        ]);
    }
}
