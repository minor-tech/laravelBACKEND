<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TripController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required'
        ]);

        $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name',
        ]));
    }

    public function show(Request $request, Trip $trip)
    {
        // if the trip is associated to the authenticated user
        if ($trip->user->id === $request->user()->id) {
            return $trip;
        }
        if ($trip->driver && $request->user()->id) {
            if ($trip->user->id === $request->driver()->id) {
                return $trip;
            }
        }

        return response()->json(['message' => 'cannot find this trip'], 404);
    }

    public function accept(Request $request, Trip $trip)
    {
        // a driver accepts a trip
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->id,
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');
    }
    public function  start(Request $request, Trip $trip)
    {
        $trip->update([
            'is_started' => true
        ]);

        $trip->load('driver.user');

        return $trip;
    }
    public function  end(Request $request, Trip $trip)
    {
        $trip->update([
            'is_comlete' => true
        ]);

        $trip->load('driver.user');

        return $trip;
    }
    public function  location(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');

        return $trip;
    }
}
