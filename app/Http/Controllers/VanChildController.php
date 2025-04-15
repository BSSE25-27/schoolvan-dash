<?php

namespace App\Http\Controllers;
use App\Models\VanChild;
use Illuminate\Http\Request;

class VanChildController extends Controller
{
    //
    public function addChildToVan ($request) {
        $validated = $request->validate([
            'VanID' => 'required|exists:vans,VanID',
            'ChildID' => 'required|exists:children,ChildID',
        ]);

        // Check if child is already assigned to a van
        if (VanChild::where([
            'VanID' => $validated['VanID'],
            'ChildID' => $validated['ChildID']
        ])->exists()) {
            return back()->withErrors(['ChildID' => 'This child is already assigned to this van'])->withInput();
        }

        VanChild::create([
            'VanID' => $validated['VanID'],
            'ChildID' => $validated['ChildID']
        ]);

        return redirect()->route('vans.index')->with('success', 'Child added to van successfully'); 
    }

    public function removeChildFromVan ($request) {
        $validated = $request->validate([
            'VanID' => 'required|exists:vans,VanID',
            'ChildID' => 'required|exists:children,ChildID',
        ]);

        // Check if child is assigned to the van
        if (!VanChild::where([
            'VanID' => $validated['VanID'],
            'ChildID' => $validated['ChildID']
        ])->exists()) {
            return back()->withErrors(['ChildID' => 'This child is not assigned to this van'])->withInput();
        }

        VanChild::where([
            'VanID' => $validated['VanID'],
            'ChildID' => $validated['ChildID']
        ])->delete();

        return redirect()->route('vans.index')->with('success', 'Child removed from van successfully');
    }
}
