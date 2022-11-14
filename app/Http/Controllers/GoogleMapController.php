<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use Illuminate\Support\Facades\Cache;

use function PHPSTORM_META\type;

class GoogleMapController extends Controller
{
    public string $googleKey;

    public function __construct()
    {
        $this->googleKey = \Config::get('googleapi.GOOGLE_MAP_KEY');
    }

    public function map()
    {
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

    public function getNearbyPlaces(Request $request)
    {
        $lat = 13.828253;
        $lng = 100.567481;

        if ($request->ajax()) {
            $searchName = $request->searchName;

            if (empty($searchName)) {
                if (Cache::has('laravel_cachedefault')) {
                    $result = Cache::get('laravel_cachedefault');
                } else {
                    $result = $this->googleNearbySearch($lat, $lng, $searchName);
                    Cache::put('laravel_cachedefault', $result, 60);
                }
            } else {
                $result[] = $this->googleTextSearch($lat, $lng, $searchName);
                Cache::put('laravel_cachede_' . $searchName, $result, 60);
            }

            return response()->json($result);
        }


        $result = '';
        $count = 0;

        return view('googlemap.nearby', compact('result', 'count'));
    }

    protected function googleNearbySearch($lat, $lng, $searchName)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=' . $lat . '%2C' . $lng . '&radius=1500&type=restaurant&key=' . $this->googleKey,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 100,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $response = json_decode($response, true);

        $token = isset($response['next_page_token']) ? $response['next_page_token'] : '';

        $dataForResponse = [];

        foreach ($response['results'] as $key => $value) {
            // if status == OPERATIONAL
            if ($value['business_status'] == 'OPERATIONAL') {
                $dataForResponse[] = [
                    'name' => $value['name'],
                    'lat' => $value['geometry']['location']['lat'],
                    'lng' => $value['geometry']['location']['lng'],
                    'place_id' => $value['place_id'],
                ];
            }
        }

        checkToken:

        $dataCount = count($dataForResponse);

        if (!empty($token) && $dataCount < 5) {

            sleep(3);

            try {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?pagetoken=' . $token . '&key=' . $this->googleKey,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 100,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
            } catch (\Exception $e) {
                return $dataForResponse;
            }

            $response = json_decode($response, true);

            $token = isset($response['next_page_token']) ? $response['next_page_token'] : '';

            foreach ($response['results'] as $key => $value) {
                if ($value['business_status'] == 'OPERATIONAL') {
                    $dataForResponse[] = [
                        'name' => $value['name'],
                        'lat' => $value['geometry']['location']['lat'],
                        'lng' => $value['geometry']['location']['lng'],
                        'place_id' => $value['place_id'],
                    ];
                }
            }
            goto checkToken;
        }

        // foreach get place detail
        foreach ($dataForResponse as $key => $value) {

            $placeId = $value['place_id'];

            $response = $this->getPlaceInfo($placeId);

            // add url, adr_address
            $dataForResponse[$key]['url'] = $response['result']['url'];
            $dataForResponse[$key]['adr_address'] = $response['result']['adr_address'];
        }

        return $dataForResponse;
    }

    protected function googleTextSearch($lat, $lng, $searchName)
    {
        // url encode search name utf-8
        $searchName = urlencode($searchName);

        $inputtype = 'textquery';

        $locationbias = 'circle%3A1500%40' . $lat . '%2C' . $lng;

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json?inputtype=textquery&input=' . $searchName . '&locationbias=circle%3A2000%4013.828253%2C100.567481&key=' . $this->googleKey,
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

            $response = json_decode($response, true);

            $place_id = isset($response['candidates'][0]['place_id']) ? $response['candidates'][0]['place_id'] : '';

            $response = $this->getPlaceInfo($place_id);

            $dataForResponse = [
                'name' => $response['result']['name'],
                'lat' => $response['result']['geometry']['location']['lat'],
                'lng' => $response['result']['geometry']['location']['lng'],
                'place_id' => $response['result']['place_id'],
                'url' => $response['result']['url'],
                'adr_address' => $response['result']['adr_address'],
            ];

            return $dataForResponse;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function getPlaceInfo($placeId)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $placeId . '&key=' . $this->googleKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        return $response;
    }
}
