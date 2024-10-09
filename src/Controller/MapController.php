<?php

namespace App\Controller;

use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]

    public function execute(): Response
    {

        $curl = curl_init();


        curl_setopt_array($curl, [
            CURLOPT_PORT => "9042",
            CURLOPT_URL => "http://localhost:9042/api/stations",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
        }

        //dump($response);

        $curl2 = curl_init();
        curl_setopt_array($curl2, [
            CURLOPT_PORT => "9042",
            CURLOPT_URL => "http://localhost:9042/api/stations/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response2 = curl_exec($curl2);

        $err2 = curl_error($curl2);

        curl_close($curl2);

        if ($err2) {
            echo "cURL Error #:" . $err2;
        }else{
            $response2 = json_decode($response2, true);
        }
        //dump($response2);

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
                    $stations[] = $stations_data; // opérateur d'assignation corrigé pour ajouter au tableau
                    // var_dump($stations);
                    break;

                }
            }
        }


//        for ($i = 0; $i < count($response); $i++)
//        {
//            $infostat = $response [$i];
//
//            for ($j = 0; $j < count($response2); $j++) {
//                $infovelo = $response2[$j];
//
//                if ($infostat['station_id'] == $infovelo['station_id']) {
//                    $stations_data = [
//                        'nom' => $infostat['name'],
//                        'lat' => $infostat['lat'],
//                        'lon' => $infostat['lon'],
//                        'velodispo' => $infovelo['num_bikes_available'],
//                        'velomecha' => $infovelo['num_bikes_available_types'][0]['mechanical'],
//                        'velomelec' => $infovelo['num_bikes_available_types'][1]['ebike']
//                    ];
//
//                    $stations[] = $stations_data;
//                    break;
//                }
//            }

        //       }

        return $this->render('map/map.html.twig', [
            "titre"   => 'MapController',
            "stations" => $stations

        ]);
    }
}