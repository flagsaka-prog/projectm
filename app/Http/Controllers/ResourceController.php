<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ResourceController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $resources = Resource::latest()->paginate(10);
        return view('resources.index', compact('resources'));
    }

    public function create()
    {
        return view('resources.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:human,equipment,material',
            'initials'     => 'nullable|string|max:10',
            'group'        => 'nullable|string|max:100',
            'max_units'    => 'nullable|numeric|min:0',
            'std_rate'     => 'nullable|numeric|min:0',
            'ovt_rate'     => 'nullable|numeric|min:0',
            'cost_per_use' => 'nullable|numeric|min:0',
            'accrue_at'    => 'nullable|in:start,end,prorated',
        ]);

        Resource::create([
            'name'         => $validated['name'],
            'type'         => $validated['type'],
            'initials'     => $validated['initials'] ?? null,
            'group'        => $validated['group'] ?? null,
            'max_units'    => $validated['max_units'] ?? 0,
            'std_rate'     => $validated['std_rate'] ?? 0,
            'ovt_rate'     => $validated['ovt_rate'] ?? 0,
            'cost_per_use' => $validated['cost_per_use'] ?? 0,
            'accrue_at'    => $validated['accrue_at'] ?? null,
        ]);

        return redirect()->route('resources.index')
            ->with('success', 'Resource berhasil ditambahkan.');
    }

    public function edit(Resource $resource)
    {
        return view('resources.edit', compact('resource'));
    }

    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:human,equipment,material',
            'initials'     => 'nullable|string|max:10',
            'group'        => 'nullable|string|max:100',
            'max_units'    => 'nullable|numeric|min:0',
            'std_rate'     => 'nullable|numeric|min:0',
            'ovt_rate'     => 'nullable|numeric|min:0',
            'cost_per_use' => 'nullable|numeric|min:0',
            'accrue_at'    => 'nullable|in:start,end,prorated',
        ]);

        $resource->update([
            'name'         => $validated['name'],
            'type'         => $validated['type'],
            'initials'     => $validated['initials'] ?? null,
            'group'        => $validated['group'] ?? null,
            'max_units'    => $validated['max_units'] ?? 0,
            'std_rate'     => $validated['std_rate'] ?? 0,
            'ovt_rate'     => $validated['ovt_rate'] ?? 0,
            'cost_per_use' => $validated['cost_per_use'] ?? 0,
            'accrue_at'    => $validated['accrue_at'] ?? null,
        ]);

        return redirect()->route('resources.index')
            ->with('success', 'Resource berhasil diupdate.');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();

        return redirect()->route('resources.index')
            ->with('success', 'Resource berhasil dihapus.');
    }
}
