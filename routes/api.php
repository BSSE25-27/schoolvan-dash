<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ParentalController;
use App\Models\Parental;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/parents/{parents}', [ParentalController::class, 'show']);
Route::get('/parents', [ParentalController::class, 'index']);
