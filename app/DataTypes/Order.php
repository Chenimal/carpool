<?php
namespace App\Http\DataTypes;

/**
 * order structure
 */
class Order
{
    private $service_type;
    private $pickup_time;
    private $delivery_time;
    private $pick_up_lat_lng;
    private $drop_off_lat_lng;

    public function __construct($info = [])
    {

    }
}
