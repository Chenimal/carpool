<?php

namespace App\Library;

use App\DataTypes\Order;

/**
 * strategy of assigning orders
 */
class Strategy
{
    /**
     * basic strategy
     * @param  array $orders
     * @param  array $vehicles
     * @return array
     */
    public static function basic($orders, $vehicles)
    {
        if (empty($orders) || count($vehicles) !== 2) {
            return;
        }
        // first lets only consider one order
        $results = [];
        foreach ($orders as $order) {
            $results[] = Location::distance($vehicles, [$order->pickup_lng, $order->pickup_lat]);
        }
        print_r($results);
        exit;
    }
}
