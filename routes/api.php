<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ParentalController;
use App\Http\Controllers\api\OperatorController;
use App\Models\Parental;
use App\Models\Operator;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/parents/{parents}', [ParentalController::class, 'show']);
Route::get('/parents', [ParentalController::class, 'index']);
Route::post('/parent-login', [ParentalController::class, 'parentLogin']);
Route::post('/operator-login', [OperatorController::class, 'operatorLogin']);
