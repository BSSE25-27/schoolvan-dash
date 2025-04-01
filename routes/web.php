<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\ParentalController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\VanController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::fallback (function(){return redirect('/dashboard');});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //driver routes
    Route::resource('all-drivers', DriverController::class)
     ->names('drivers')
     ->parameters(['all-drivers' => 'driver']);

    Route::resource('all-parents', ParentalController::class)
     ->names('parents')
     ->parameters(['all-parents' => 'parent']);

    Route::resource('all-children', ChildController::class)
     ->names('children')
     ->parameters(['all-children' => 'children']);
    
    Route::resource('all-vans', VanController::class)
     ->names('vans')
     ->parameters(['all-vans' => 'vans']);
    
    Route::resource('all-trips', TripController::class)
     ->names('trips')
     ->parameters(['all-trips' => 'trip']);
    
    Route::resource('all-operators', OperatorController::class)
     ->names('operators')
     ->parameters(['all-operators' => 'operator']);
});

require __DIR__.'/auth.php';
