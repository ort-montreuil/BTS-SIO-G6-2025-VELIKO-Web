<?php

namespace App\Controller;

//use http\Env\Response;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Symfony\Component\Routing\Attribute\Route;
class MapController extends AbstractController
{
    #[Route('/map')]

    public function map() : Response
    {
        session_start();

        $curl = curl_init();


        curl_setopt_array($curl, [
            CURLOPT_URL => "https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json",
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

        $curl2 = curl_init();
        curl_setopt_array($curl2, [
            CURLOPT_URL => "https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_status.json",
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

        $stations = [];
        foreach ($response['data']['stations'] as $infostat) {
            foreach ($response2['data']['stations'] as $infovelo) {
                if ($infostat['station_id'] == $infovelo['station_id']) {
                    $stations_data = [
                        'nom' => $infostat['name'],
                        'lat' => $infostat['lat'],
                        'lon' => $infostat['lon'],
                        'velodispo' => $infovelo['numBikesAvailable'],
                        'velomecha' => $infovelo['num_bikes_available_types'][0]['mechanical'],
                        'velomelec' => $infovelo['num_bikes_available_types'][1]['ebike']
                    ];
                    $stations[] = $stations_data; // opérateur d'assignation corrigé pour ajouter au tableau
                    // var_dump($stations);
                    break;

                }
            }
        }

        return $this->render('map/map.html.twig', [
                "titre"   => 'MapController',
                "stations" => $stations

            ]);
    }
}
