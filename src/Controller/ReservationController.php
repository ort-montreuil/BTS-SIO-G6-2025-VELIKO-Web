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
    #[Route('/reserver', name: 'app_reservation')]
    public function reserverVelo(UserRepository $userRepository, Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_map');
        }


        $form = $this->createForm(ReservationFormType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            //location velo: venir retirer le velo

            $idStationDepart = $form->get('idStationDepart')->getData()->getStationId();
            $typeVelo = $form->get('typeVelo')->getData();
            $idStationArrivee = $form->get('idStationArrivee')->getData()->getStationId();

            $response = $this->makeCurl("/api/velos", "GET", "");

            foreach ($response as $velo) {
                $idVelo = $velo["velo_id"];

                // Vérifier si le vélo est disponible à la station de départ
                if ((int) $velo["station_id_available"] == (int) $idStationDepart
                    && $velo["status"] == "available"
                    && ($velo["type"] == $typeVelo)) {

                    // Mettre le vélo en location
                    $this->makeCurl("/api/velo/{$idVelo}/location", "PUT", "RG6F8do7ERFGsEgwkPEdW1Feyus0LXJ21E2EZRETTR65hN9DL8a3O8a");

                    $majResponse = $this->makeCurl("/api/velos", "GET", "");
                    foreach ($majResponse as $veloMaj)
                    // Vérifier si le vélo est en location et doit être ramené à la station de fin
                    if ((int) $veloMaj["station_id_available"] != (int) $idStationArrivee
                        && $veloMaj["status"] == "location") {
                        // Restauration du vélo à la station de fin
                        $this->makeCurl("/api/velo/{$idVelo}/restore/{$idStationArrivee}", "PUT", "RG6F8do7ERFGsEgwkPEdW1Feyus0LXJ21E2EZRETTR65hN9DL8a3O8a");

                    }else{
                        $this->addFlash('danger', 'Pas de possibilité de remettre le vélo à la station d\'arrivée');
                    }
                    $reservation = $form->getData();
                    $reservation->setIdStationDepart($form->get('idStationDepart')->getData()->getId());
                    $reservation->setIdStationArrivee($form->get('idStationArrivee')->getData()->getId());
                    $reservation->setEmailUser($this->getUser()->getEmail());
                    $manager->persist($reservation);
                    $manager->flush();

                    $this->addFlash('success', 'Votre réservation a été effectuée avec succès !');


                    return $this->redirectToRoute('app_map');
                }else{
                    $this->addFlash('danger', 'Pas de vélo disponible à la station de départ');
                }
            }
        }else{
            $this->addFlash('danger', 'Veuillez remplir correctement le formulaire');
        }
        return $this->render('reservation/reserver.html.twig', [
            'reservationForm' => $form->createView(),
        ]);
    }



    public function makeCurl(string $url, string $methode, string $token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => "9042",
            CURLOPT_URL => $_ENV["API_VELIKO_URL"] . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $methode,
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
                "Authorization:" . $token]
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
        }
        return $response;
    }


}