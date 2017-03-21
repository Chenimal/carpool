<?php
namespace App\DataTypes;

use DB;

/**
 * order structure
 */
class Order
{
    // required input fields when creating order
    private $service_type;
    private $pickup_time;
    private $delivery_time;
    private $pickup_lat_lng;
    private $dropoff_lat_lng;

    // order id
    private $id;

    /**
     * create new order
     * @param  array
     */
    public function __construct($input = [])
    {
        // validation
        if (empty($input['service_type'])) {
            abort(500, 'Unknown order\'s service_type');
        }
        $this->service_type = constant("App\DataTypes\ServiceType::{$input['service_type']}");
        if (empty($input['pickup_time']) || !strtotime($input['pickup_time'])) {
            abort(500, 'Invalid order\'s pickup time');
        }
        $this->pickup_time = $input['pickup_time'];
        if (empty($input['delivery_time']) || !strtotime($input['delivery_time'])) {
            abort(500, 'Invalid order\'s delivery time');
        }
        if ($input['pickup_time'] >= $input['delivery_time']) {
            abort(500, 'Order\'s delivery time must greater than pickup time');
        }
        $this->delivery_time = $input['delivery_time'];
        if (empty($input['pickup_lat_lng']) || empty($input['pickup_lat_lng'][0]) || empty($input['pickup_lat_lng'][1])) {
            abort(500, 'Unknown order\'s pickup coordinate');
        }
        $this->pickup_lat_lng = $input['pickup_lat_lng'];
        if (empty($input['dropoff_lat_lng']) || empty($input['dropoff_lat_lng'][0]) || empty($input['dropoff_lat_lng'][1])) {
            abort(500, 'Unknown order\'s drop off coordinate');
        }
        $this->dropoff_lat_lng = $input['dropoff_lat_lng'];

        // saving order into db
        $this->id = DB::table('orders')
            ->insertGetId([
                'service_type'  => $this->service_type,
                'pickup_time'   => $this->pickup_time,
                'delivery_time' => $this->delivery_time,
                'pickup_lat'    => $this->pickup_lat_lng[0],
                'pickup_lng'    => $this->pickup_lat_lng[1],
                'dropoff_lat'   => $this->dropoff_lat_lng[0],
                'dropoff_lng'   => $this->dropoff_lat_lng[1],
            ]);
    }

    /**
     * get info for display on the map
     * @param  none
     * @return array
     */
    public function getInfo()
    {
        return [
            'id'              => $this->id,
            'service_type'    => $this->service_type,
            'pickup_time'     => $this->pickup_time,
            'delivery_time'   => $this->delivery_time,
            'pickup_lat_lng'  => $this->pickup_lat_lng,
            'dropoff_lat_lng' => $this->dropoff_lat_lng,
        ];
    }

    /**
     * search order by order_id
     * @param  int order_id
     * @return self
     */
    public static function searchByOrderId($order_id)
    {
        // todo: db related
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
