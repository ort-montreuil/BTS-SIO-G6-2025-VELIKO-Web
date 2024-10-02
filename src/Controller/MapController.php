<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function execute(): Response
    {
        // Premier appel cURL pour les stations
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_PORT => "9042",
            CURLOPT_URL => "http://localhost:9042/api/stations",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "Erreur cURL : " . $err;
            $response = false; // Gérer l'erreur d'une autre manière si nécessaire
        } else {
            $response = json_decode($response, true);
        }

        // Deuxième appel cURL pour le statut des vélos
        $curl2 = curl_init();
        curl_setopt_array($curl2, [
            CURLOPT_PORT => "9042",
            CURLOPT_URL => "http://localhost:9042/api/stations/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response2 = curl_exec($curl2);
        $err2 = curl_error($curl2);
        curl_close($curl2);

        if ($err2) {
            echo "Erreur cURL : " . $err2;
            $response2 = false; // Gérer l'erreur d'une autre manière si nécessaire
        } else {
            $response2 = json_decode($response2, true);
        }

        // Vérification avant de boucler sur les réponses
        if ($response === false) {
            echo "Erreur lors de la récupération des données des stations.";
        } elseif ($response2 === false) {
            echo "Erreur lors de la récupération des données de statut des vélos.";
        } else {
            $stations = [];

            foreach ($response as $infostat) {
                foreach ($response2 as $infovelo) {
                    if ($infostat['station_id'] == $infovelo['station_id']) {
                        $stations_data = [
                            'nom' => $infostat['name'],
                            'lat' => $infostat['lat'],
                            'lon' => $infostat['lon'],
                            'velodispo' => $infovelo['num_bikes_available'],
                            'velomecha' => $infovelo['num_bikes_available_types'][0]['mechanical'],
                            'velomelec' => $infovelo['num_bikes_available_types'][1]['ebike']
                        ];
                        $stations[] = $stations_data;
                        break;
                    }
                }
            }

            return $this->render('map/map.html.twig', [
                "titre"   => 'MapController',
                "stations" => $stations
            ]);
        }

        // Si une erreur survient, retourner une réponse vide ou d'erreur
        return new Response("Erreur lors de l'affichage des stations.");
    }
}