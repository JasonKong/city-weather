<?php

namespace App\Http\Controllers;

use App\Util\AjaxResponse;
use App\Util\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CityController extends Controller
{
    //
    public function __construct()
    {
        $this->_rsp = new AjaxResponse();
    }

    /**
     * Home page for weather search
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        return view('home');
    }

    /**
     * search similar cities
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {

        $term = $request->input('term');
        $url = Config::get('app.teleport_api_url').'cities/?search='.$term;

        //$ch = curl_init();

        $rsp = $this->makeRequest($url);

        if ($rsp) {
            if (isset($rsp['count']) && $rsp['count'] > 0) {
                if(isset($rsp['_embedded']['city:search-results'])) {
                    $cities = $this->_getCitiesList($rsp['_embedded']['city:search-results']);
                    $this->_rsp->data['cities'] = $cities;
                }
            } else {
                $this->_rsp->success = false;
                $this->_rsp->error_msg = 'Search City Failed';
            }
        } else {
            $this->_rsp->success = false;
            $this->_rsp->error_msg = 'Search City Failed';
        }

        return $this->_rsp->jsonResponse();
    }

    /**
     * retrieve city data
     *
     * @param $data
     * @return array
     */
    private function _getCitiesList($data) {
        $_cities = [];

        foreach($data as $key=>$datum) {
            if(isset($datum['matching_full_name']) && isset( $datum['_links']['city:item']['href'])) {
                $city = [
                    'id' => $key,
                    'full_name' => $datum['matching_full_name'],
                    'detail_link' => $datum['_links']['city:item']['href']
                ];
                $_cities[] = $city;
            }
        }
        return $_cities;
    }

    /**
     * Get weekly weather of a city
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCityWeather(Request $request) {

        $detail_link = $request->input('link');

        $location = $this->_getCityLocation($detail_link);

        if ($location) {
            $latitude = $location['latlon']['latitude'];
            $longitude = $location['latlon']['longitude'];

            $url = Config::get('app.darksky.url').$latitude.','.$longitude.'??exclude=currently,minutely,hourly';

            $rsp = $this->makeRequest($url);

            if ($rsp) {
                $this->_rsp->data['weathers'] = $this->_getWeatherData($rsp['daily']['data']);
            }
        }

        return $this->_rsp->jsonResponse();
    }

    /**
     * Get city location
     *
     * @param $detail_link
     * @return bool
     */
    public function _getCityLocation($detail_link) {

        $_rsp = $this->makeRequest($detail_link);

        if ($_rsp) {
            if (isset($_rsp['location'])) {
                return $_rsp['location'];
            }
        }
        return false;
    }

    /**
     * Retriev weather data
     *
     * @param $data
     * @return array
     */
    private function _getWeatherData($data) {
        $weather_data = [];

        $celsius = json_decode('"\u2103"');

        foreach($data as $key => $datum) {

            if ($key > 6) break;

            // convert to celsius
            $t_high = round(($datum['temperatureHigh'] - 32) * 5 / 9);
            $t_low = round(($datum['temperatureLow'] - 32) * 5 / 9);

            $weather = [
                'date' => date('d M', $datum['time']),
                'summary' => $datum['summary'],
                'icon' =>  'images/icons/'.$datum['icon'].'.svg',
                'temperature' => $t_low .' ~ '. $t_high .$celsius,
                'high_temperature' => $t_high,
                'low_temperature' => $t_low
            ];
            $weather_data[] = $weather;
        }

        return $weather_data;
    }
}
