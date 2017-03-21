<?php
namespace App\DataTypes;

use DB;

/**
 * Class to manipulate order
 * (singleton mode)
 */
class Order
{
    private static $instance;

    private function __construct()
    {}

    public static function instance()
    {
        if (!isset(self::$instance)) {
            $c              = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    public function __clone()
    {
        trigger_error('Clone is not allow', E_USER_ERROR);
    }

    /**
     * create new order
     * @param  array
     */
    public function create($input = [])
    {
        // validation
        if (empty($input['service_type'])) {
            throw new \Exception('Unknown order\'s service_type');
        }
        $input['service_type'] = constant("App\DataTypes\ServiceType::{$input['service_type']}");
        if (empty($input['pickup_time']) || !strtotime($input['pickup_time'])) {
            throw new \Exception('Invalid order\'s pickup time');
        }
        if (empty($input['delivery_time']) || !strtotime($input['delivery_time'])) {
            throw new \Exception('Invalid order\'s delivery time');
        }
        if ($input['pickup_time'] >= $input['delivery_time']) {
            throw new \Exception('Order\'s delivery time must greater than pickup time');
        }
        if (empty($input['pickup_lat_lng']) || empty($input['pickup_lat_lng'][0]) || empty($input['pickup_lat_lng'][1])) {
            throw new \Exception('Unknown order\'s pickup coordinate');
        }
        if (empty($input['dropoff_lat_lng']) || empty($input['dropoff_lat_lng'][0]) || empty($input['dropoff_lat_lng'][1])) {
            throw new \Exception('Unknown order\'s drop off coordinate');
        }

        // saving order into db
        $input['id'] = DB::table('orders')
            ->insertGetId([
                'service_type'  => $input['service_type'],
                'pickup_time'   => $input['pickup_time'],
                'delivery_time' => $input['delivery_time'],
                'pickup_lat'    => $input['pickup_lat_lng'][0],
                'pickup_lng'    => $input['pickup_lat_lng'][1],
                'dropoff_lat'   => $input['dropoff_lat_lng'][0],
                'dropoff_lng'   => $input['dropoff_lat_lng'][1],
            ]);
        return $input;
    }

    /**
     * search order by order_id
     * @param  int order_id
     * @return object or null
     */
    public function getOrderById($order_id)
    {
        if (empty($order_id)) {
            throw new \Exception('Invalid order_id');
        }
        $result = DB::table('orders')
            ->where('id', $order_id)
            ->first();
        return $result;
    }

    /**
     * remove order
     * @param  int order_id
     * @return  boolean
     */
    public function takeOrder($order_id)
    {
        // todo: db related
    }

}
