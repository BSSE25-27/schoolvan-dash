<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controller\api\BaseApiController;
use App\Models\Parental;
use Illuminate\Http\Request;

class ParentProfileController extends Controller
{
    //
    public function getProfile(Request $request)
    {
        $apiKey = $request->header('X-API-KEY');
    
        if (!$apiKey) {
            return response()->json(['error' => 'API key required'], 401);
        }
    
        $parent = Parental::where('api_key', $apiKey)->first();
        
        if (!$parent) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }
    
        $children = $parent->children()->get(['ChildName']);
    

        return response()->json([
            'parent_name' => $parent->ParentName,
            'email' => $parent->Email,
            'phone_number' => $parent->PhoneNumber,
            'children' => $children
        ]);
    }
}
