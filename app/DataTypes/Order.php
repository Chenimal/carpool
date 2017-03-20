<?php
namespace App\DataTypes;

/**
 * order structure
 */
class Order
{
    // required fields when creating order
    private $service_type;
    private $pickup_time;
    private $delivery_time;
    private $pick_up_lat_lng;
    private $drop_off_lat_lng;

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
        if (empty($input['pick_up_lat_lng']) || empty($input['pick_up_lat_lng'][0]) || empty($input['pick_up_lat_lng'][1])) {
            abort(500, 'Unknown order\'s pickup coordinate');
        }
        $this->pick_up_lat_lng = $input['pick_up_lat_lng'];
        if (empty($input['drop_off_lat_lng']) || empty($input['drop_off_lat_lng'][0]) || empty($input['drop_off_lat_lng'][1])) {
            abort(500, 'Unknown order\'s drop off coordinate');
        }
        $this->drop_off_lat_lng = $input['drop_off_lat_lng'];

        // todo: code of saving order into db below
    }

    public function getInfo()
    {
        return [
            'service_type'     => $this->service_type,
            'pickup_time'      => $this->pickup_time,
            'delivery_time'    => $this->delivery_time,
            'pick_up_lat_lng'  => $this->pick_up_lat_lng,
            'drop_off_lat_lng' => $this->drop_off_lat_lng,
        ];
    }
}
