<?php

namespace App\Http\Controllers;
use App\Models\VanChild;
use App\Models\Van;
use App\Models\Child;
use Illuminate\Http\Request;

class VanChildController extends Controller
{
    //
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'created_at'); // Default sorting by creation date

        // Fetch assignments with sorting by operator name if requested
        $assignments = VanChild::with(['van.operator', 'child.parent'])
            ->when($sort === 'VanOperatorName', function ($query) {
                $query->join('vans', 'vans.VanID', '=', 'van_children.VanID')
                      ->join('operators', 'vans.OperatorID', '=', 'operators.OperatorID')
                      ->orderBy('operators.VanOperatorName');
            })
            ->select('van_children.*') // Ensure only `van_children` fields are selected
            ->latest()
            ->paginate(10);

        $vans = Van::with('operator')->get();
        $children = Child::with('parent')->get();

        return view('assignment.index', compact('assignments', 'vans', 'children'));
    }

    public function addChildToVan(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'VanID' => 'required|exists:vans,VanID',
            'ChildID' => 'required|exists:children,ChildID',
        ]);

        // Check if the child is already assigned to the van
        if (VanChild::where('ChildID', $validated['ChildID'])->exists()) {
            return back()->withErrors(['ChildID' => 'This child is already assigned to a van'])->withInput();
        }

        // Create the assignment
        VanChild::create($validated);

        return redirect()->route('assignment.index')->with('success', 'Child assigned to van successfully.');
    }

    public function removeChildFromVan($id)
{
    // Find the assignment by ID
    $assignment = VanChild::findOrFail($id);

    // Delete the assignment
    $assignment->delete();

    return redirect()->route('assignment.index')->with('success', 'Child removed from van successfully.');
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'VanID' => 'required|exists:vans,VanID',
        'ChildID' => 'required|exists:children,ChildID',
    ]);

    $assignment = VanChild::findOrFail($id);

    // Check if the child is already assigned to another van
    if (VanChild::where('ChildID', $validated['ChildID'])->where('id', '!=', $id)->exists()) {
        return back()->withErrors(['ChildID' => 'This child is already assigned to another van'])->withInput();
    }

    $assignment->update($validated);

    return redirect()->route('assignment.index')->with('success', 'Assignment updated successfully.');
}

public function edit($id)
{
    $assignment = VanChild::with(['child.parent', 'van.operator'])->findOrFail($id);
    $vans = Van::with('operator')->get();
    $children = Child::with('parent')->get();

    return view('assignment.edit', compact('assignment', 'vans', 'children'));
}
}
