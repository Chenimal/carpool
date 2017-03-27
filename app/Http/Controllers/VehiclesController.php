<?php

namespace App\Http\Controller;

use App\Library\Location;
use Illuminate\Http\Request;

class VehiclesController extends Controller
{
    /**
     * get vehicle randomly
     * @param none
     * @return  json/jsonp
     */
    public function random(Request $request)
    {
        $vehicle_info = [
            'id'      => mt_rand(1, 1000),
            'lat_lng' => Location::createRandomAccessibleLocation(),
        ];

        $response = response()->json($vehicle_info);
        // jsonp
        if ($request->input('jsonp')) {
            $response->setCallback($request->input('jsonp'));
        }
        return $response;
    }
}
