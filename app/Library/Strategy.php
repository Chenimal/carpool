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
     * @return 0 or 1 (vehicle index)
     */
    public static function basic($orders, $vehicles)
    {
        if (empty($orders) || count($vehicles) !== 2) {
            return;
        }

        $total_duration = [
            0 => 0,
            1 => 0,
        ];
        $total_distance = [
            0 => 0,
            1 => 0,
        ];

        /**
         * don't consider time
         * 1. consider 1 order(done)
         * 2. consider 2 orders:
         */
        foreach ($orders as $order) {
            $distances = Location::distance($vehicles, [$order->pickup_lng, $order->pickup_lat]);
            foreach ($distances as $distance) {
                // not accessible
                if (isset($item->info)) {
                    continue;
                }
                // 一个订单不需要考虑 该订单的开始-结束花费，只需要考虑pickup的
                $total_distance[$distance->origin_id - 1] += $distance->distance;
                $total_duration[$distance->origin_id - 1] += $distance->duration;
            }
        }
        $least_duration    = array_keys($total_duration, min($total_duration));
        $shortest_distance = array_keys($total_distance, min($total_distance));
        var_dump($total_duration, $total_distance, $least_duration, $shortest_distance);
        exit;
    }
}
