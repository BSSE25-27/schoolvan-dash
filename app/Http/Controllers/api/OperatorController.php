<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operator;
use Illuminate\Support\Str;

class OperatorController extends Controller
{
    //
    public function operatorLogin(Request $request) {
        $validated = $request->validate([
            'VanOperatorName' => 'required|string|max:255',
            'PhoneNumber' => 'required|string|max:20',// Make sure this matches your frontend field name
        ]);
    
        $operator = Operator::where('VanOperatorName', $validated['VanOperatorName'])
            ->where('PhoneNumber', $validated['PhoneNumber']) // Using the validated data
            ->first();
    
        if (!$operator) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        // Generate API key if doesn't exist
        if (empty($operator->api_key)) {
            $operator->api_key = Str::random(64);
            $operator->save();
        }
        return response()->json([
            'message' => 'Login successful',
            'operator' => $operator,
        ]);
    }

    public function getProfile(Request $request){
        $apiKey = $request->header('X-API-KEY');
    
        if (!$apiKey) {
            return response()->json(['error' => 'API key required'], 401);
        }
    
        $operator = Operator::where('api_key', $apiKey)->first();
        
        if (!$operator) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }
    
        return response()->json([
            'operator_name' => $operator->VanOperatorName,
            'email' => $operator->Email,
            'phone_number' => $operator->PhoneNumber,
        ]);
    }
}
