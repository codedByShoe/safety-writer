<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PackageController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $packages = Package::ordered()->get();

        return Inertia::render('admin/packages/Index', [
            'packages' => $packages,
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return Inertia::render('admin/packages/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:packages,slug',
            'description' => 'nullable|string',
            'stripe_price_id' => 'required|string|max:255',
            'credits' => 'required|integer|min:1',
            'price' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        Package::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit(Request $request, Package $package): Response
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return Inertia::render('admin/packages/Edit', [
            'package' => $package,
        ]);
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:packages,slug,'.$package->id,
            'description' => 'nullable|string',
            'stripe_price_id' => 'required|string|max:255',
            'credits' => 'required|integer|min:1',
            'price' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $package->update($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(Request $request, Package $package): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted successfully.');
    }
}
