<?php
namespace App\Http\Controllers;

use App\DataTypes\Order;

/**
 * order related api
 */
class OrdersController extends Controller
{
    /**
     * create order randomly
     * @param none
     * @return  json
     */
    public function create()
    {
        // pickup and delivery time is always end with 0, 15, 30, 45
        $time_interval = 15 * 60;
        // pickup_time is in the next hour: curren_time < pickup_time <= current_time + 1 hrs
        $max_pickup_span = 60 * 60;
        // deliveryTime - pickupTime <= 6 hrs
        $max_delivery_span = 6 * 60 * 60;

        $pickup_timestamp   = (floor(time() / $time_interval) + mt_rand(1, $max_pickup_span / $time_interval)) * $time_interval;
        $delivery_timestamp = $pickup_timestamp + mt_rand(1, $max_delivery_span / $time_interval) * $time_interval;
        $input              = [
            // service_type could be A, B, or C
            'service_type'     => chr(ord('A') + mt_rand(0, 2)),
            'pickup_time'      => date('Y-m-d H:i:s', $pickup_timestamp),
            'delivery_time'    => date('Y-m-d H:i:s', $delivery_timestamp),
            'pick_up_lat_lng'  => ['114.53254', '21.2314214'],
            'drop_off_lat_lng' => ['114.2341', '23.4543253'],
        ];
        $order = new Order($input);

        return response()->json($order->getInfo());
    }

    /**
     * remove order on the map
     * @param int order_id
     * @return [type] [description]
     */
    public function remove($order_id)
    {

    }

    /**
     * assign an order to a specific vehicle
     * @param  int $order_id
     * @param  int $vehicle_id
     * @return boolean
     */
    public function match($order_id, $vehicle_id)
    {

    }
}
