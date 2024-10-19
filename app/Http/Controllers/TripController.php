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
}
