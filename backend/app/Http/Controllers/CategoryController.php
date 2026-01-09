<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->withCount('brands')
            ->get();

        return response()->json($categories);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->with(['brands' => function ($query) {
                $query->active()->verified()->latest()->limit(20);
            }])
            ->firstOrFail();

        return response()->json($category);
    }
}
