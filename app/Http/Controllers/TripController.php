<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Van;

class TripController extends Controller
{
    //
    public function index()
    {
        // Read - Display a list of items
        $trips = Trip::latest()->paginate(10);
        return view('trips.index', compact('trips'));
    }
    public function create($request)
    {
        $request->validate([
            'vanOperatorId' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $van = Van::where('VanOperatorID', $request->vanOperatorId)->first();

        // Create - Show the form to create a new item
        $current_trip = Trip::where('VanID', $van->VanID)
            ->where('date', date('Y-m-d'))
            ->where('trip_status', 'ongoing')
            ->first();

        if ($current_trip) {
            $current_trip->current_latitude = $request->latitude;
            $current_trip->current_longitude = $request->longitude;
            $current_trip->save();
        } else {
            $current_trip = new Trip();
            $current_trip->VanID = $van->VanID;
            $current_trip->start_latitude = $request->latitude;
            $current_trip->start_longitude = $request->longitude;
            $current_trip->date = date('Y-m-d');
            $current_trip->start_time = date('H:i:s');
            $current_trip->trip_status = 'ongoing';
            $current_trip->is_complete = false;
            $current_trip->is_started = true;
            $current_trip->save();
        }

        return response()->json([
            'status' => 'Success',
        ]);
        
    }
    public function store(Request $request)
    {
        // Create - Save a new item to the database
    }
    public function show($id)
    {
        // Read - Display a single item
    }
    public function edit($id)
    {
        // Update - Show the form to edit an item
    }
    public function update(Request $request, $id)
    {
        // Update - Save the edited item to the database
    }
    public function destroy($id)
    {
        // Delete - Remove an item from the database
    }

    public function completeTrip(Request $request, $id)
    {
        // Complete the trip
        $trip = Trip::findOrFail($id);
        $trip->is_complete = true;
        $trip->end_time = now();
        $trip->save();

        return response()->json([
            'message' => 'Trip completed successfully',
            'trip' => $trip,
        ]);
    }

}
