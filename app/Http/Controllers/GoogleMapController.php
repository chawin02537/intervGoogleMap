<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;

class GoogleMapController extends Controller
{
    public function map()
    {
        return view('googlemap.index');
    }

    public function getLocationFromText(Request $request)
    {
        $curl = curl_init();

        $searchName = $request->name;
        // replace spaces with + sign
        $searchName = str_replace(' ', '+', $searchName);

        $circle = 100;

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=' . $searchName . '&inputtype=textquery&fields=geometry,formatted_address&locationbias=point:50,10&key=' . env('GOOGLE_MAP_KEY'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // get lat and lng
        $data = json_decode($response, true);
        $lat = $data['candidates'][0]['geometry']['location']['lat'];
        $lng = $data['candidates'][0]['geometry']['location']['lng'];

        // dd($request->name, $lat, $lng);

        return view('googlemap.index');
    }

    /*
    Missing
    (Controller)
    - radius selected
    - gps current location
    - name search
    - cache api result
    - jwt for api

    (View)
    - search input
    - marker info bug
    - more beautiful
    */

    public function getNearbyPlaces()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=13.828253%2C100.5284507&radius=100&type=restaurant&key=' . env('GOOGLE_MAP_KEY'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 100,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $dataForSend = [];

        // get name and lat and lng
        $data = json_decode($response, true);

        $count = 1;

        foreach ($data['results'] as $key => $value) {
            $name = $value['name'];
            $lat = $value['geometry']['location']['lat'];
            $lng = $value['geometry']['location']['lng'];
            // add to data array
            $dataForSend[] = [
                'name' => $name,
                'lat' => $lat,
                'lng' => $lng,
                'count' => $count
            ];

            $count++;
        }

        // dd($dataForSend);
        return view('googlemap.nearby', compact('dataForSend'));
    }
}
