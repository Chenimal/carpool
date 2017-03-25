<?php

namespace Library;

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
        $map_center = config('app.map.center');
        return $map_center;
    }
}
