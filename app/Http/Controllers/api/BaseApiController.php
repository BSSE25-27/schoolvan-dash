<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parental;
use App\Models\Operator;

class BaseApiController extends Controller
{
    //

    protected $parent;
    
    public function __construct()
    {
        $this->authenticate();
    }
    
    protected function authenticate()
    {
        $apiKey = request()->header('X-API-KEY') ?? request()->input('api_key');
        
        if (!$apiKey) {
            abort(response()->json(['error' => 'API key required'], 401));
        }

        $this->parent = Parental::where('api_key', $apiKey)->first();

        if (!$this->parent) {
            abort(response()->json(['error' => 'Invalid API key'], 401));
        }
    }

    protected $operator;
    public function __constructOperator()
    {
        $this->authenticateOperator();
    }
    protected function authenticateOperator()
    {
        $apiKey = request()->header('X-API-KEY') ?? request()->input('api_key');
        
        if (!$apiKey) {
            abort(response()->json(['error' => 'API key required'], 401));
        }

        $this->operator = Operator::where('api_key', $apiKey)->first();

        if (!$this->operator) {
            abort(response()->json(['error' => 'Invalid API key'], 401));
        }
    }
}
