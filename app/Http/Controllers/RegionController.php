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

    public function getRegions(Request $request)
    {

        $perPage = $request->input('per_page', 10);

        $regions = Region::with(['creator'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

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

    public function searchRegions(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = Region::query();

        if ($request->has('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        if ($request->has('region')) {
            $query->where('region', 'like', '%' . $request->region . '%');
        }

        $regions = $query->with(['creator'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($regions);
    }
}
