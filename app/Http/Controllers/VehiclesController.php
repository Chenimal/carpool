<?php

namespace App\Http\Controllers;

use App\Library\Location;
use Illuminate\Http\Request;

class VehiclesController extends Controller
{
    /**
     * get 2 vehicle randomly
     * @param none
     * @return  json/jsonp
     */
    public function getRandom(Request $request)
    {
        $vehicle_info = [
            Location::createRandomAccessibleLocation(),
            Location::createRandomAccessibleLocation(),
        ];

        $response = response()->json($vehicle_info);
        // jsonp
        if ($request->input('jsonp')) {
            $response->setCallback($request->input('jsonp'));
        }
        return $response;
    }
}
