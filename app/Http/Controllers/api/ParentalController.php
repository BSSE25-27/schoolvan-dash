<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
// use App\Http\Controllers\api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\Parental;
use App\Models\Child;
use App\Models\Trip;
use App\Models\VanChild;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @group Parent Management
 *
 * APIs for managing all parents.
 */
class ParentalController extends Controller
{
 
    public function show(Request $request, Parental $parents){
        return response()->json([
            'success' => true,
            'data' => $parents
        ]);
    }

    public function index(){
        
            $parents = Parental::all();
            return response()->json($parents);
        
    }

    public function parentLogin(Request $request)
    {
        $request->validate([
            'Email' => 'required|email',
            'ParentName' => 'required|string',
        ]);

        $parent = Parental::where('Email', $request->Email)
            ->where('ParentName', $request->ParentName)
            ->first();

        if (!$parent) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate API key if doesn't exist
        if (empty($parent->api_key)) {
            $parent->api_key = Str::random(64);
            $parent->save();
        }

        return response()->json([
            'message' => 'Login successful',
            'api_key' => $parent->api_key,
            'parent' => $parent,
        ]);
    }

    public function parentLogout(Request $request)
    {
        $apiKey = $request->header('X-API-KEY') ?? $request->input('api_key');
        $parent = Parental::where('api_key', $apiKey)->first();
        
        if ($parent) {
            $parent->api_key = null;
            $parent->save();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function getChildrenLocation (Request $request) {
        $childId = $request->validate([
            'ChildID' => 'required|integer'
        ]);

        $child = Child::find($childId['ChildID']);

        $childVan = VanChild::where('ChildID', $childId['ChildID'])->first()
            ->first();
        if (!$childVan) {
            return response(null,400)->json([
                'success' => false,
                'message' => 'Child not found in any van',
            ]);
        }
        
        $child_trip = Trip::where('VanID', $childVan->VanID)
            ->where('date', date('Y-m-d'))
            ->where('trip_status', 'ongoing')
            ->first();

        $childLocations[] = [
            'ChildID' => $child->ChildID,
            'ChildName' => $child->ChildName,
            'VanID' => null,
            'has_location' => false,
            'current_latitude' => null,
            'current_longitude' => null,
        ];

        Log::info('Child location request', [
            'childId' => $childId['ChildID'],
            'child' => $child,
            'trip' => $child_trip,
        ]);
        
            
       if(isset($child_trip)) {
        $childLocation = [
            'ChildID' => $child->ChildID,
            'ChildName' => $child->ChildName,
            'VanID' => $childVan->VanID,
            'has_location' => true,
            // 'is_at_home' => $child_trip->type == 'home_pickup' ? true : false,
            // 'is_at_school' => $child_trip->type == 'school_pickup' ? true : false,
            'current_latitude' => $child_trip->current_latitude,
            'current_longitude' => $child_trip->current_longitude,
        ];
    }

        

        return response()->json([
            'success' => true,
            'childLocation' => $childLocation,
        ]);
    }

    
}
