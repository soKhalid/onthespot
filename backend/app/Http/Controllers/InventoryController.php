<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Brand $brand)
    {
        $items = $brand->inventoryItems()
            ->available()
            ->orderBy('sort_order')
            ->get();

        return response()->json($items);
    }

    public function store(Request $request, Brand $brand)
    {
        $this->authorize('update', $brand);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
            'category' => 'nullable|string',
        ]);

        $item = $brand->inventoryItems()->create([
            ...$request->all(),
            'slug' => \Illuminate\Support\Str::slug($request->name),
        ]);

        return response()->json($item, 201);
    }

    public function update(Request $request, Brand $brand, InventoryItem $item)
    {
        $this->authorize('update', $brand);

        if ($item->brand_id !== $brand->id) {
            return response()->json(['error' => 'Item does not belong to this brand'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        $item->update($request->all());

        return response()->json($item);
    }

    public function destroy(Brand $brand, InventoryItem $item)
    {
        $this->authorize('update', $brand);

        if ($item->brand_id !== $brand->id) {
            return response()->json(['error' => 'Item does not belong to this brand'], 403);
        }

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}
