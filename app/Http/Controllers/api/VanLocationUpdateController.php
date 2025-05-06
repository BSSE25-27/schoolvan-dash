<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Van;
use App\Events\VanLocationUpdated;
use App\Models\VanChild;

class VanLocationUpdateController extends Controller
{
    //
    public function updateLocation(Request $request)
    {
        $request->validate([
            'van_id' => 'required|exists:vans,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $van = Van::with('children')->find($request->van_id);
        $childIds = $van->children->pluck('id')->toArray();
        
        // Broadcast the event
        event(new VanLocationUpdated(
            $request->van_id,
            $request->latitude,
            $request->longitude,
            $childIds
        ));
        
        return response()->json(['status' => 'success']);
    }
}
