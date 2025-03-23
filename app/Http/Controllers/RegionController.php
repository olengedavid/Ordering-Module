<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegionController extends Controller
{
    public function index()
    {
        return Inertia::render('Supplier/Region');
    }

    public function getRegions()
    {
        $regions = Region::with(['creator'])->get();
        return response()->json($regions);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'created_by' => 'required|exists:users,id',
        ]);

        Region::create($validated);
        return redirect()->back()->with('success', 'Region created successfully');
    }

    public function update(Request $request, $uuid)
    {
        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'updated_by' => 'required|exists:users,id',
        ]);

        $region = Region::where('uuid', $uuid)->firstOrFail();
        $region->update($validated);

        return redirect()->back()->with('success', 'Region Update successfully');
    }

    public function destroy($uuid)
    {
        $region = Region::where('uuid', $uuid)->firstOrFail();
        $region->delete();

        return redirect()->back()->with('success', 'Region Deleted successfully');
    }
}
