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
        'center'     => [22.310816, 114.1727589],
        'north_east' => [22.50823614007428, 114.45359691269528],
        'south_west' => [22.11311632940318, 113.89192088730465],
    ],
];
