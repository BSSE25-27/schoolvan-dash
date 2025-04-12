<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parental;

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

    public function parentLogin(Request $request) {
    $validated = $request->validate([
        'ParentName' => 'required|string|max:255',
        'Email' => 'required|email' // Make sure this matches your frontend field name
    ]);

    $parent = Parental::where('ParentName', $validated['ParentName'])
        ->where('Email', $validated['Email']) // Using the validated data
        ->first();

    if (!$parent) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    return response()->json([
        'message' => 'Login successful',
        'parent' => $parent,
    ]);
}
}
