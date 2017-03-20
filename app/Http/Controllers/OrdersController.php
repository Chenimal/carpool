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

        $pickup_timestamp   = time() + mt_rand(0, 24 * 60 * 60);
        $delivery_timestamp = $pickup_timestamp + mt_rand(0, 6 * 60 * 60);
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
}
