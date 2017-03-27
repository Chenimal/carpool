<?php

return [
    // server timezone
    'timezone'                    => 'Asia/Hong_Kong',

    // pickup time is always end up with 0, 15, 30, 45
    'pickup_time_interval'        => 15 * 60,
    // pickup_time is in the next hour: curren_time < pickup_time <= current_time + 1 hrs
    'max_span_bt_now_pickup'      => 60 * 60,
    // deliveryTime - pickupTime <= 6 hrs
    'max_span_bt_pickup_delivery' => 6 * 60 * 60,

    // map related
    'map'                         => [
        'base_url'  => 'http://restapi.amap.com/v3/',
        'key'       => 'd3aecf166c87cbf1d642eaf05f465a28',
        'locations' => [
            'center'     => [114.1727589, 22.310816],
            'north_east' => [114.45359691269528, 22.50823614007428],
            'south_west' => [113.89192088730465, 22.11311632940318],
        ],
    ],
];
