<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Van;
use App\Events\VanLocationUpdated;
use App\Models\Trip;
use App\Models\VanChild;
use Illuminate\Support\Facades\Log;

class VanLocationUpdateController extends Controller
{
    //
    public function updateLocation(Request $request)
    {
        $request->validate([
            'vanOperatorId' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $van = Van::where('VanOperatorID', $request->vanOperatorId)->first();
        $vanChildren = VanChild::where('VanID', $van->VanID)->get();
        $childIds = [];
        foreach ($vanChildren as $child) {
            $childIds[] = $child->ChildID;
        }

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
            $current_trip->type = $request->type;
            $current_trip->start_latitude = $request->latitude;
            $current_trip->start_longitude = $request->longitude;
            $current_trip->date = date('Y-m-d');
            $current_trip->start_time = date('H:i:s');
            $current_trip->trip_status = 'ongoing';
            $current_trip->is_complete = false;
            $current_trip->is_started = true;
            $current_trip->save();
        }
        

        Log::info('Van location updated', [
            'vanOperatorId' => $request->vanOperatorId,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'child_ids' => $childIds,
            'current_trip' => $current_trip,
        ]);
        
        
        return response()->json(['status' => 'success']);
    }
}
