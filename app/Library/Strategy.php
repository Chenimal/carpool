<?php

namespace App\Library;

use App\DataTypes\Order;

/**
 * strategy of assigning orders
 */
class Strategy
{
    private static $sub_section_distances;

    /**
     * basic strategy
     * @param  array $orders
     * @param  array $vehicles
     * @return 0 or 1 (vehicle index)
     */
    public static function basic($order_ids, $vehicles, $conditions = null)
    {
        if (empty($order_ids) || count($vehicles) !== 2) {
            return;
        }
        // get all sub-section distances from map api (高德地图),
        self::subSectionDistances($order_ids, $vehicles);

        // get all possible splits of orders
        $splits = self::splits($order_ids);

        $solutions = [];
        foreach ($splits as $split) {
            $sequences_vehicle_0 = self::sequences($split[0]);
            $sequences_vehicle_1 = self::sequences($split[1]);

            $v0 = self::bestSequence($sequences_vehicle_0, 0, $conditions);
            $v1 = self::bestSequence($sequences_vehicle_1, 1, $conditions);

            $solutions[] = [
                'duration' => [
                    'total'    => $v0['duration']['duration'] + $v1['duration']['duration'],
                    'sequence' => [isset($v0['duration']['key']) ? $sequences_vehicle_0[$v0['duration']['key']] : [], isset($v1['duration']['key']) ? $sequences_vehicle_1[$v1['duration']['key']] : []],
                    'duration' => [$v0['duration']['duration'], $v1['duration']['duration']],
                    'distance' => [$v0['duration']['distance'], $v1['duration']['distance']],
                ],
                'distance' => [
                    'total'    => $v0['distance']['distance'] + $v1['distance']['distance'],
                    'sequence' => [isset($v0['distance']['key']) ? $sequences_vehicle_0[$v0['distance']['key']] : [], isset($v1['distance']['key']) ? $sequences_vehicle_1[$v1['distance']['key']] : []],
                    'duration' => [$v0['distance']['duration'], $v1['distance']['duration']],
                    'distance' => [$v0['distance']['distance'], $v1['distance']['distance']],
                ],
            ];
        }
        $result = self::leastCostSolution($solutions);
        return $result;
    }

    /**
     * find out the shortest sequence from given sollution
     * @param  array $solutions
     * @return array $solution
     */
    protected static function leastCostSolution($solutions)
    {
        $min = [
            'duration' => [
                'total'    => 0,
                'sequence' => [],
                'duration' => [],
                'distance' => [],
            ],
            'distance' => [
                'total'    => 0,
                'sequence' => [],
                'duration' => [],
                'distance' => [],
            ],
        ];
        foreach ($solutions as $s) {
            if ($s['duration']['total'] != 0 && ($min['duration']['total'] == 0 || $s['duration']['total'] < $min['duration']['total'])) {
                $min['duration'] = $s['duration'];
            }
            if ($s['distance']['total'] != 0 && ($min['distance']['total'] == 0 || $s['distance']['total'] < $min['distance']['total'])) {
                $min['distance'] = $s['distance'];
            }
        }
        return $min;
    }

    /**
     * find out the shortest sequence from given sequences
     * @param  array $sequences
     * @param  int 0 or 1, vehicle index
     * @return array min
     */
    protected static function bestSequence($sequences, $vehicle_index, $conditions = null)
    {
        $min = [
            'duration' => [
                'key'      => null,
                'duration' => null,
                'distance' => null,
            ],
            'distance' => [
                'key'      => null,
                'duration' => null,
                'distance' => null,
            ],
        ];
        foreach ($sequences as $key => $sequence) {
            if (empty($sequence)) {
                continue;
            }

            if (!empty($conditions)) {
                $meet_conditions = self::checkConditions($sequence, $conditions);
                if (!$check_filter) {
                    continue;
                }
            }

            $duration = 0;
            $distance = 0;

            // vehicle to the 1st point
            $vehicle_to_1st_point = self::$sub_section_distances['vehicle_' . $vehicle_index][$sequence[0]];
            $duration += $vehicle_to_1st_point->duration;
            $distance += $vehicle_to_1st_point->distance;

            $cnt = count($sequence);
            for ($i = 0; $i < $cnt - 1; $i++) {
                $sub_distance = self::$sub_section_distances[$sequence[$i]][$sequence[$i + 1]];
                $duration += $sub_distance->duration;
                $distance += $sub_distance->distance;
            }
            if (!isset($min['duration']['duration']) || $duration < $min['duration']['duration']) {
                $min['duration'] = [
                    'key'      => $key,
                    'duration' => $duration,
                    'distance' => $distance,
                ];
            }
            if (!isset($min['distance']['distance']) || $distance < $min['distance']['distance']) {
                $min['distance'] = [
                    'key'      => $key,
                    'duration' => $duration,
                    'distance' => $distance,
                ];
            }
        }
        return $min;
    }

    /**
     * check if the sequence meet the given condition(s)
     * @param  array $sequence
     * @param  array $conditions
     * @return boolean true or false
     */
    protected static function checkConditions($sequence, $conditions)
    {
        if (empty($conditions)) {
            return true;
        }
        foreach ($conditions as $k => $c) {
            if ($k == 'NO_MORE_THAN_ORIGINAL_DURATION') {

            }
            if ($k == 'NO_MORE_THAN_ORIGINAL_DISTANCE') {

            }
        }
    }

    /**
     * find out all possible ways of spliting given orders into two vehicles
     * @param array $order_ids
     * @return array of possible splits: [[order_ids_for_vehicle_a, order_ids_for_vehicle_b], ...]
     */
    protected static function splits($order_ids)
    {
        // maximum&minimum number of orders a vehicle could have at a time
        $max_num_orders = min(3, count($order_ids));
        $min_num_orders = max(0, count($order_ids) - $max_num_orders);

        $splits = [];
        for ($i = $min_num_orders; $i <= $max_num_orders; $i++) {
            $splits_vehicle_a = math_combination($order_ids, $i);
            foreach ($splits_vehicle_a as $combination_a) {
                $splits[] = [$combination_a, array_diff($order_ids, $combination_a)];
            }
        }
        return $splits;
    }

    /**
     * find out all possible sequences for given orders
     * @param array order_ids
     * @return array e.g. ['order_1_start','order_2_start','order_2_end','order_1_end']
     */
    protected static function sequences($order_ids)
    {
        $points = [];
        foreach ($order_ids as $id) {
            $points[] = [$id . '_start', $id . '_end'];
        }
        $sequences = math_sequence($points);
        return $sequences;
    }

    /**
     * find out all possible distance & duration between sub-sections of orders
     * @param array order_ids
     * @param array vehicles
     * @return array of start1 => [end1, end2, end3]
     * e.g. [
     *    'order1_start'=>[
     *        'order1_end'=>['distance'=> 100000, 'duration'=>3600],
     *        'order2_end'=>['distance'=>4600,'duration'=>600],
     *        'order2_start'=>['distance'=>7200,'duration'=>800],
     *     ],...
     *  ]
     */
    protected static function subSectionDistances($order_ids, $vehicles)
    {
        if (isset(self::$sub_section_distances)) {
            return self::$sub_section_distances;
        }
        $points = [];
        foreach ($order_ids as $id) {
            $points[] = [$id . '_start', $id . '_end'];
        }
        $vectors = math_vector($points);

        $orders  = Order::getOrderById($order_ids);
        $mapping = [];
        foreach ($orders as $o) {
            $mapping[$o->id . '_start'] = [$o->pickup_lng, $o->pickup_lat];
            $mapping[$o->id . '_end']   = [$o->dropoff_lng, $o->dropoff_lat];
        }

        $request = [];
        foreach ($vectors as $end => $starts) {
            $end_lng_lat    = $mapping[$end];
            $starts_lng_lat = [];
            foreach ($starts as $s) {
                $starts_lng_lat[] = $mapping[$s];
            }
            $starts_lng_lat[] = $vehicles[0];
            $starts_lng_lat[] = $vehicles[1];
            $starts[]         = 'vehicle_0';
            $starts[]         = 'vehicle_1';

            $request[$end] = [
                'destination' => $end_lng_lat,
                'origins'     => array_combine($starts, $starts_lng_lat),
            ];
        }
        $result = Location::distanceBatch($request);

        $i                 = 0;
        $result_with_index = [];
        foreach ($request as $end_index => $r) {
            $result_with_index[$end_index] = array_combine(array_keys($r['origins']), $result[$i++]);
        }
        // distances got from api is xx_end => xx_start, convert to xx_start => xx_end
        $result = [];
        foreach ($result_with_index as $end => $starts) {
            foreach ($starts as $start => $d) {
                if (!isset($result[$start])) {
                    $result[$start] = [];
                }
                $result[$start][$end] = $d;
            }
        }
        self::$sub_section_distances = $result;
        return self::$sub_section_distances;
    }
}
