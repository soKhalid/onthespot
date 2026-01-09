<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()
            ->favorites()
            ->with('category')
            ->get();

        return response()->json($favorites);
    }

    public function toggle(Request $request, Brand $brand)
    {
        $user = $request->user();

        if ($user->favorites()->where('brand_id', $brand->id)->exists()) {
            $user->favorites()->detach($brand->id);
            return response()->json(['message' => 'Removed from favorites', 'is_favorite' => false]);
        } else {
            $user->favorites()->attach($brand->id);
            return response()->json(['message' => 'Added to favorites', 'is_favorite' => true]);
        }
    }
}
