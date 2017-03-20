<?php
namespace App\Http\Controllers;

class OrdersController extends Controller
{
    /**
     * create order randomly
     * @param none
     * @return  json
     */
    public function create()
    {
        // service type could be A, B, or C
        $service_type = chr(ord('A') + mt_rand(0, 2));

        $new_order = [
            'serviceType'   => constant("App\DataTypes\ServiceType::$service_type"),
            'pickupTime'    => '13:00',
            'deliveryTime'  => '14:50',
            'pickUpLatLng'  => ['114.53254', '21.2314214'],
            'dropOffLatLng' => ['114.2341', '23.4543253'],
        ];

        return response()->json($new_order);
    }
}
