<?php

namespace App\Http\Controllers;

use App\Util\Curl;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $_rsp;

    public function makeRequest($url, $method = 'GET', $options = []) {

        try {
            $rsp = Curl::exec($url);
            if ($rsp) {
                return json_decode($rsp, true);
            } else {
                return false;
            }
        } catch(\Exception $e) {

        }
    }
}
