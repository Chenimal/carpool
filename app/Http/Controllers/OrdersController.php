<?php
namespace App\Http\Controllers;

use App\DataTypes\Order;
use App\Library\Location;
use App\Library\Strategy;
use Illuminate\Http\Request;

/**
 * order related api
 */
class OrdersController extends Controller
{
    /**
     * create order randomly
     * @param none
     * @return  json/jsonp
     */
    public function createRandom(Request $request)
    {
        // pickup time is always end up with 0, 15, 30, 45
        $time_interval = config('app.pickup_time_interval');
        // pickup_time is in the next hour: curren_time < pickup_time <= current_time + 1 hrs
        $max_span_bt_now_pickup = config('app.max_span_bt_now_pickup');

        // random values:
        $pickup_lng_lat     = Location::createRandomAccessibleLocation();
        $dropoff_lng_lat    = Location::createRandomAccessibleLocation();
        $pickup_timestamp   = (floor(time() / $time_interval) + mt_rand(1, $max_span_bt_now_pickup / $time_interval)) * $time_interval;
        $delivery_timestamp = $pickup_timestamp + Location::distance([$pickup_lng_lat], $dropoff_lng_lat)[0]->duration;

        $input = [
            // service_type could be A, B, or C
            'service_type'    => chr(ord('A') + mt_rand(0, 2)),
            'pickup_time'     => $pickup_timestamp,
            'pickup_lng_lat'  => $pickup_lng_lat,
            'delivery_time'   => $delivery_timestamp,
            'dropoff_lng_lat' => $dropoff_lng_lat,
        ];
        $order = Order::instance()->create($input);

        $order['pickup_time']   = date('Y-m-d H:i:s', $order['pickup_time']);
        $order['delivery_time'] = date('Y-m-d H:i:s', $order['delivery_time']);
        $response               = response()->json($order);
        // jsonp
        if ($request->input('jsonp')) {
            $response->setCallback($request->input('jsonp'));
        }
        return $response;
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
     * @param  int/array $order_ids
     * @param  array[[vehicle_1_lng,vehicle_1_lat],[vehicle_2_lng,vehicle_2_lat]]
     * @return boolean
     */
    public function assign(Request $request)
    {
        $order_ids = $request->input('order_ids');
        $vehicles  = $request->input('vehicles');

        $result = Strategy::subSectionDistances($order_ids, $vehicles);
        exit;
        $response = response()->json(
            [
                'orders'   => $orders,
                'vehicles' => $vehicles,
            ]
        );

        // jsonp
        if ($request->input('jsonp')) {
            $response->setCallback($request->input('jsonp'));
        }
        return $response;
    }
}
