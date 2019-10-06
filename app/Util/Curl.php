<?php

namespace App\Util;

class Curl
{
    private static $error = '';
    private static $error_no = 0;

    /**
     * @return string
     */
    public static function getError(): string
    {
        return self::$error;
    }

    /**
     * @return int
     */
    public static function getErrorNo(): int
    {
        return self::$error_no;
    }

    public static function exec($url, $options = [
        'TIMEOUT' => 60,
        'CONNECTTIMEOUT' => 30,
        'POST' => 2,
        'HEADER' => false
    ], $fields = false, $header = false)
    {

        self::$error = '';
        self::$error_no = 0;

        $ch = curl_init();

        if ($header && is_array($header)) {
            $header_arr = [];

            foreach ($header as $h_i => $h_v) {
                $header_arr[] = $h_i . ': ' . $h_v;
            }

            $options['HTTPHEADER'] = $header_arr;
        }

        foreach ($options as $option => $value) {
            curl_setopt($ch, constant('CURLOPT_' . $option), $value);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//        if (isset($options['CUSTOMREQUEST']) && $options['CUSTOMREQUEST'] == 'GET') {
//            array_map('urlencode', $fields);
//            $url = $url . "?" . http_build_query($fields);
//        } elseif ($fields) {
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
//        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_URL, $url);

        if (\App::environment('dev')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        if ($response = curl_exec($ch)) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_code != 200 && $http_code != 201) {
                self::$error_no = $http_code;
                self::$error = $response;

                $response = false;
            }
        } else {
            $response = false;
            self::$error_no = curl_errno($ch);
            self::$error = curl_error($ch);
        }

        @curl_close($ch);

        return $response;
    }
}
