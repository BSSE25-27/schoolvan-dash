<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Van;

class VanController extends Controller
{
    //
    public function index()
    {
        $vans = Van::latest()->paginate(10);
        return view('vans.index', compact('vans'));
    }

    // Show the form for creating a new van
    public function create()
    {
        // You can return a view here if needed
        return view('vans.create');
    }

    // Store a newly created van in the database
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'NumberPlate' => 'required|string|max:255',
            'Longitude' => 'required|numeric',
            'Latitude' => 'required|numeric',
            'VanOperatorID' => 'required|exists:vanoperators,VanOperatorID',
            'DriverID' => 'required|exists:drivers,DriverID',
        ]);

        // Create the new van
        $van = Van::create([
            'NumberPlate' => $validated['NumberPlate'],
            'Longitude' => $validated['Longitude'],
            'Latitude' => $validated['Latitude'],
            'VanOperatorID' => $validated['VanOperatorID'],
            'DriverID' => $validated['DriverID'],
        ]);

        return response()->json(['message' => 'Van created successfully', 'van' => $van], 201);
    }

    // Display the specified van
    public function show($id)
    {
        $van = Van::findOrFail($id); // Retrieves van by id
        return response()->json($van);
    }

    // Show the form for editing the specified van
    public function edit($id)
    {
        // You can return a view here if needed
        $van = Van::findOrFail($id);
        return view('vans.edit', compact('van'));
    }

    // Update the specified van in the database
    public function update(Request $request, $id)
    {
        // Validate input
        $validated = $request->validate([
            'NumberPlate' => 'required|string|max:255',
            'Longitude' => 'required|numeric',
            'Latitude' => 'required|numeric',
            'VanOperatorID' => 'required|exists:vanoperators,VanOperatorID',
            'DriverID' => 'required|exists:drivers,DriverID',
        ]);

        // Find the van and update it
        $van = Van::findOrFail($id);
        $van->update([
            'NumberPlate' => $validated['NumberPlate'],
            'Longitude' => $validated['Longitude'],
            'Latitude' => $validated['Latitude'],
            'VanOperatorID' => $validated['VanOperatorID'],
            'DriverID' => $validated['DriverID'],
        ]);

        return response()->json(['message' => 'Van updated successfully', 'van' => $van]);
    }

    // Remove the specified van from the database
    public function destroy($id)
    {
        $van = Van::findOrFail($id);
        $van->delete();

        return response()->json(['message' => 'Van deleted successfully']);
    }
}
