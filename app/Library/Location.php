<?php

namespace App\Library;

/**
 * location service
 */
class Location
{
    /**
     * createRandomAccessibleLocation
     * @return array[lng, lat]
     */
    public static function createRandomAccessibleLocation()
    {
        $map = config('app.map');

        $base_lng = $map['locations']['south_west'][0];
        $base_lat = $map['locations']['south_west'][1];
        $span_lng = $map['locations']['north_east'][0] - $base_lng;
        $span_lat = $map['locations']['north_east'][1] - $base_lat;

        $base_url = $map['base_url'] . 'distance?key=' . $map['key'] . '&destination=' . implode(',', $map['locations']['center']);

        while (1) {
            $random_spot = [];
            // 高德 Api only support up to 100 origins
            for ($i = 0; $i < 100; $i++) {
                $random_spot[] = ($base_lng + $span_lng * mt_rand() / mt_getrandmax())
                    . ',' . ($base_lat + $span_lat * mt_rand() / mt_getrandmax());
            }
            $url    = $base_url . '&origins=' . implode('|', $random_spot);
            $result = self::curlGet($url);
            if ($result->status != 1) {
                return false;
            }
            foreach ($result->results as $item) {
                if (!isset($item->info)) {
                    return explode(',', $random_spot[$item->origin_id + 1]);
                }
            }
        }
    }

    /**
     * measure distance between origin(s) and destination
     * @param  array[latLng, ...] $origins
     * @param  lagLng
     * @return
     */
    public static function distance($origins, $destination)
    {
        $map = config('app.map');

        $origins_str = [];
        foreach ($origins as $origin) {
            $origins_str[] = implode(',', $origin);
        }
        $url    = $map['base_url'] . 'distance?key=' . $map['key'] . '&origins=' . implode('|', $origins_str) . "&destination=" . implode(',', $destination);
        $result = self::curlGet($url);
        return $result->results;
    }

    /**
     * measure distance between origin(s) and destination
     * multi destinations
     */
    public static function distanceBatch($request)
    {
        $map = config('app.map');

        $urls = [];
        foreach ($request as $end_index => $r) {
            $origins_str = [];
            foreach ($r['origins'] as $start_index => $origin) {
                $origins_str[] = implode(',', $origin);
            }
            $urls[] = ['url' => '/v3/distance?key=' . $map['key'] . '&origins=' . implode('|', $origins_str) . '&destination=' . implode(',', $r['destination'])];
        }
        $post_url  = $map['base_url'] . 'batch?key=' . $map['key'];
        $post_data = json_encode(['ops' => $urls]);

        $result   = self::curlPost($post_url, $post_data);
        $response = [];
        foreach ($result as $r) {
            $response[] = $r->body->results;
        }
        return $response;
    }

    /**
     * curl get request
     */
    protected static function curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:1080');

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return json_decode($response);
    }

    protected static function curlPost($url, $data = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        $result = curl_getinfo($ch);
        curl_close($ch);
        return json_decode($response);
    }
}
