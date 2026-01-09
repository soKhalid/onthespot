<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::with(['category', 'user'])
            ->active()
            ->verified();

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        // Filter by location (nearby)
        if ($request->has('latitude') && $request->has('longitude')) {
            $radius = $request->get('radius', 10); // Default 10km
            $query->nearby($request->latitude, $request->longitude, $radius);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy !== 'distance') {
            $query->orderBy($sortBy, $sortOrder);
        }

        return response()->json($query->paginate(20));
    }

    public function show($slug)
    {
        $brand = Brand::with(['category', 'inventoryItems' => function ($query) {
            $query->available()->orderBy('sort_order');
        }, 'reviews' => function ($query) {
            $query->approved()->latest()->limit(10);
        }])
        ->where('slug', $slug)
        ->firstOrFail();

        return response()->json($brand);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'business_hours' => 'nullable|array',
        ]);

        $brand = $request->user()->brands()->create([
            ...$request->all(),
            'slug' => \Illuminate\Support\Str::slug($request->name),
        ]);

        return response()->json($brand, 201);
    }

    public function update(Request $request, Brand $brand)
    {
        $this->authorize('update', $brand);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'business_hours' => 'nullable|array',
            'talabat_url' => 'nullable|url',
            'deliveroo_url' => 'nullable|url',
        ]);

        $brand->update($request->all());

        return response()->json($brand);
    }

    public function destroy(Brand $brand)
    {
        $this->authorize('delete', $brand);

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully']);
    }

    public function myBrands(Request $request)
    {
        $brands = $request->user()->brands()->with('category')->get();

        return response()->json($brands);
    }
}
