<?php

namespace App\Library;

/**
 * location service
 */
class Location
{
    /**
     * createRandomAccessibleLocation
     * @return array[lat,lng]
     */
    public static function createRandomAccessibleLocation()
    {
        $map = config('app.map');

        $span_lng = $map['locations']['north_east'][0] - $map['locations']['south_west'][0];
        $span_lat = $map['locations']['north_east'][1] - $map['locations']['south_west'][1];

        $status = false;
        while (!$status) {
            $random_spot = [
                $map['locations']['south_west'][0] + $span_lng * mt_rand() / mt_getrandmax(),
                $map['locations']['south_west'][1] + $span_lat * mt_rand() / mt_getrandmax(),
            ];

            $url    = $map['base_url'] . 'direction/driving?key=' . $map['key'] . '&origin=' . implode(',', $map['locations']['center']) . '&destination=' . implode(',', $random_spot);
            $result = self::curlGet($url);
            $status = $result->status;
        }
        return $random_spot;
    }

    /**
     * curl get request
     */
    public static function curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:1080');

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return json_decode($response);
    }
}
