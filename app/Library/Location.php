<?php

namespace App\Library;

use DB;

/**
 * location service
 */
class Location
{
    private static $key;
    private static $num_available_keys = 4;

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

        $bounds = $map['bounds'];
        /**
         * rayCasting algorithm to determine if the point is inside a polygon
         */
        while (1) {
            $random_point = [
                $base_lng + $span_lng * mt_rand() / mt_getrandmax(),
                $base_lat + $span_lat * mt_rand() / mt_getrandmax(),
            ];
            $flag   = false;
            $length = count($bounds);
            for ($i = 0, $j = $length - 1; $i < $length; $j = $i, $i++) {
                $p1 = $bounds[$j];
                $p2 = $bounds[$i];
                // the random point is at bound point
                if (($random_point[0] == $p1[0] && $random_point[1] == $p1[1]) || $random_point[0] == $p2[0] && $random_point[1] == $p2[1]) {
                    return $random_point;
                }
                if (($random_point[1] > $p1[1] && $random_point[1] <= $p2[1]) || ($random_point[1] > $p2[1] && $random_point[1] <= $p1[1])) {
                    $x = $p2[0] + ($random_point[1] - $p2[1]) * ($p1[0] - $p2[0]) / ($p1[1] - $p2[1]);
                    // random_point is on the side
                    if ($x == $random_point[0]) {
                        return $random_point;
                    }
                    // the ray is intersect with border
                    if ($x > $random_point[0]) {
                        $flag = !$flag;
                    }
                }
            }
            if ($flag) {
                return $random_point;
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
        $key = self::getKey();

        $origins_str = [];
        foreach ($origins as $origin) {
            $origins_str[] = implode(',', $origin);
        }

        for ($i = 0; $i < self::$num_available_keys; $i++) {
            $url    = $map['base_url'] . 'distance?key=' . $key->key . '&origins=' . implode('|', $origins_str) . "&destination=" . implode(',', $destination);
            $result = self::curlGet($url);
            if ($result->info == 'DAILY_QUERY_OVER_LIMIT') {
                $key = self::updateKey();
                continue;
            }
            return $result->results;
        }
        throw new \Exception('The Gaode map api reached the limitation. Please contact me.');
    }

    /**
     * measure distance between origin(s) and destination
     * multi destinations
     */
    public static function distanceBatch($request)
    {
        $map = config('app.map');
        $key = self::getKey();

        for ($i = 0; $i < self::$num_available_keys; $i++) {
            $urls = [];
            foreach ($request as $end_index => $r) {
                $origins_str = [];
                foreach ($r['origins'] as $start_index => $origin) {
                    $origins_str[] = implode(',', $origin);
                }
                $urls[] = ['url' => '/v3/distance?key=' . $key->key . '&origins=' . implode('|', $origins_str) . '&destination=' . implode(',', $r['destination'])];
            }
            $post_url  = $map['base_url'] . 'batch?key=' . $key->key;
            $post_data = json_encode(['ops' => $urls]);

            $result = self::curlPost($post_url, $post_data);

            $succed   = true;
            $response = [];
            foreach ($result as $r) {
                if ($r->body->info == 'DAILY_QUERY_OVER_LIMIT') {
                    $key    = self::updateKey();
                    $succed = false;
                    break;
                }
                $response[] = $r->body->results;
            }
            if ($succed) {
                return $response;
            }
        }
        throw new \Exception('The Gaode map api reached the limitation. Please contact me.');
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

    /**
     * get map api key
     * @return string
     */
    protected static function getKey()
    {
        if (!isset(self::$key)) {
            self::$key = DB::table('gaode_map_keys')
                ->where('in_use', 1)
                ->first();
        }
        return self::$key;
    }
    /**
     * update api key
     * @return string
     */
    protected static function updateKey()
    {
        $new_id = (self::$key->id + 1) % self::$num_available_keys;
        DB::table('gaode_map_keys')
            ->where('id', '!=', $new_id)
            ->update([
                'in_use' => 0,
            ]);
        DB::table('gaode_map_keys')
            ->where('id', $new_id)
            ->update([
                'in_use' => 1,
            ]);
        self::$key = DB::table('gaode_map_keys')
            ->where('id', $new_id)
            ->first();
        return self::$key;
    }
}
