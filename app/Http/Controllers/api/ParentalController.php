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
}
