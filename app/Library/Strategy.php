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

    /**
     * find out all possible ways of spliting orders to two vehicles
     * @param array $order_ids
     * @return array of possible splits: [[order_ids_for_vehicle_a, order_ids_for vehicle_b], ...]
     */
    public static function allSplits($order_ids)
    {
        // maximum&minimum number of orders a vehicle could have at a time
        $max_num_orders = min(3, count($order_ids));
        $min_num_orders = max(0, count($order_ids) - $max_num_orders);

        $splits = [];
        for ($i = $min_num_orders; $i < $max_num_orders; $i++) {
            $splits_vehicle_a = math_combination($order_ids, $i);
            $splits[]         = [$splits_vehicle_a, array_diff($order_ids, $splits_vehicle_a)];
        }
        // when order=1
        var_dump($splits);
        exit;
    }

    /**
     * find out all possible sequences for each split
     * @param array [order_ids_for_vehicle_a, order_ids_for vehicle_b]
     * @return array e.g. ['order_1_start','order_2_start','order_2_end','order_1_end']
     */
    public static function allSequences($split)
    {

    }

    /**
     * find out distance&duration between any spot of orders
     */
    public static function subSectionDistance($order_ids, $vehicles)
    {

    }
}
